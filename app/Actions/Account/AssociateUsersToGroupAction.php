<?php

namespace App\Actions\Account;

use App\Enum\ParticipantType;
use App\Enum\SavedSearches;
use App\Events\EventContactCreated;
use App\Helpers\AdvancedSearch\ContactAdvancedSearchHelper;
use App\Helpers\CsvHelper;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\Group;
use App\Models\GroupContact;
use App\Models\User;
use MetaFramework\Traits\Responses;
use Throwable;

class AssociateUsersToGroupAction
{
    use Responses;

    public function associateUsersToGroupByEventContact(): array
    {
        $group_id = request('associateUsersToGroupByEventContact.group_id');
        $eventContactIds = CsvHelper::csvToUniqueArray(request('ids'));
        $userIds = EventContact::whereIn('id', $eventContactIds)
            ->pluck('user_id')
            ->toArray();
        return $this->associateUsersToGroupByUserIds($group_id, $userIds);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function associateUsersToGroupByUserIds(int $groupId, array $userIds): array
    {
        $this->enableAjaxMode();

        $group = Group::findOrFail($groupId);
        if (null === $group) {
            $this->responseError('Group not found');
            return $this->fetchResponse();
        }


        try {

            $mode = request('mode');
            if ('all' === $mode) {
                $userIds = null;
                $searchFilters = session('savedSearch.' . SavedSearches::CONTACTS->value . '.filters');
                if (null !== $searchFilters) {
                    $userIds = ContactAdvancedSearchHelper::getUserIdsBySearchFilters($searchFilters);
                }
            }
            if (is_null($userIds)) {
                $userIds = User::all()->pluck('id')->toArray();
            }


            $nbAssociations = 0;
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    GroupContact::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'group_id' => $groupId,
                        ]
                    );
                    $nbAssociations++;
                }
            }
            if ($nbAssociations > 0) {
                $this->responseSuccess("Les contacts ont bien été affectés au groupe \"{$group->name}\"");
            } else {
                $this->responseWarning("Aucun contact n'a été affecté au groupe \"{$group->name}\"");
            }

            return $this->fetchResponse();

        } catch (Throwable $e) {
            $this->responseException($e);
        }
        return $this->fetchResponse();
    }
}
