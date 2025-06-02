<?php

namespace App\Actions\EventManager\Accommodation;


use App\Accessors\EventManager\Availability;
use App\Models\EventManager\EventGroup;
use App\Models\EventManager\Groups\BlockedGroupRoom;
use Illuminate\Support\Facades\DB;
use MetaFramework\Traits\Ajax;
use MetaFramework\Traits\Responses;
use Throwable;

class BlockGroupRooms
{
    use Ajax;
    use Responses;

    private EventGroup $model;
    private array $data;
    private int $records = 0;
    private array $updated = [];

    public function __construct(public int $event_group_id)
    {
        $this->setEventGroupId();
    }

    public function process(): array
    {
        if ($this->hasErrors()) {
            return $this->fetchResponse();
        }

        $this->parseData();


        DB::beginTransaction();

        try {
            foreach ($this->data as $value) {
                $this->processModel($value);
            }

            if ($this->records > 0) {
                $this->responseSuccess(__('ui.record_updated'));
                $this->responseElement('updated', $this->updated);
                $this->response['callback'] = request('callback');
            }

            DB::commit();
        } catch (Throwable $e) {
            $this->responseException($e);
            DB::rollBack();
        }

        return $this->fetchResponse();
    }

    private function setEventGroupId(): void
    {
        try {
            $this->model = EventGroup::findOrFail($this->event_group_id);
        } catch (Throwable) {
            $this->responseError("Aucun évènement ne peut pas être retrouvé avec l'identifiant #".$this->event_group_id);
        }
    }

    private function parseData()
    {
        if ( ! request()->has('group')) {
            $this->responseError("Aucun rattachement à traiter");
        }

        $groups = array_unique(request('group'));
        foreach ($groups as $group) {
            foreach (request($group) as $values) {
                if (empty($values['total'])) {
                    continue;
                }
                $subdata                           = [];
                $subdata['event_group_id']         = request('event_group_id');
                $subdata['event_accommodation_id'] = $values['hotel_id'];
                $subdata['room_group_id']          = $values['room_group_id'];
                $subdata['group_key']              = $group;
                $subdata['date']                   = $values['date'];
                $subdata['total']                  = $values['total'];
                if (isset($values['id'])) {
                    $subdata['id'] = $values['id'];
                }
                $this->data[] = $subdata;
            }
        }
    }

    private function processModel(array $value): void
    {
        $availability = (new Availability())
            ->setEventAccommodation($value['event_accommodation_id'])
            ->setEventGroupId($value['room_group_id']);

        $summary = $availability->getAvailability();
        $rooms   = $availability->getRoomGroups();

        $avaialble  = $summary[$value['date']][$value['room_group_id']] ?? 0;
        $hasBlocked = $this->model->blockedRooms->filter(fn($item) => $item->date == $value['date'] && $item->room_group_id == $value['room_group_id'])->first()?->total ?: 0;

        $totalAvailable = $avaialble + $hasBlocked;

        if ($totalAvailable < $value['total']) {
            $this->responseError("Le total saisi de ".$value['total']." pour ".$rooms[$value['room_group_id']]['name']." est supérieur à la disponibilité actuelle de ".$totalAvailable.".");
        } else {
            $model = BlockedGroupRoom::firstOrNew([
                'id'                     => $value['id'] ?? null,
                'event_accommodation_id' => $value['event_accommodation_id'],
            ]);

            $model->date           = $value['date'];
            $model->event_group_id = $this->model->id;
            $model->room_group_id  = $value['room_group_id'];
            $model->group_key      = $value['group_key'];
            $model->total          = $value['total'];

            $model->save();
            $this->records += 1;


            $this->updated[] = ['group' => $model->group_key, 'total' => $model->total];
        }
    }


}
