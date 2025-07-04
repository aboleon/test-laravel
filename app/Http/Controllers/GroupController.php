<?php

namespace App\Http\Controllers;

use App\Accessors\GroupAccessor;
use App\DataTables\GroupDataTable;
use App\Enum\SavedSearches;
use App\Http\Requests\GroupRequest;
use App\Models\AdvancedSearchFilter;
use App\Models\EventManager\EventGroup;
use App\Models\Group;
use App\Models\GroupAddress;
use App\Traits\DataTables\MassDelete;
use App\Validation\GoogleAddressComboValidator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Services\Validation\ValidationTrait;
use Throwable;

class GroupController extends Controller
{
    use MassDelete;
    use ValidationTrait;

    public function index(GroupDataTable $dataTable): JsonResponse|View
    {
        return $dataTable->render(
            'groups.index',
            [
                'searchFilters' => AdvancedSearchFilter::getFilters(SavedSearches::GROUPS->value),
            ],
        );
    }

    public function create(): Renderable
    {
        $eventId = request('event_id');

        return view('groups.edit')
            ->with([
                'data'     => new Group(),
                'eventId'  => $eventId,
                'contacts' => [],
            ]);
    }

    public function store(GroupRequest $request): RedirectResponse
    {
        if ( ! $this->ensureDataIsValid($request, 'group')) {
            return $this->sendResponse();
        }
        GoogleAddressComboValidator::validate(request()->all());
        $waGeoData = request('wa_geo');


        $this->validated_data['group']['created_by'] = auth()->id();

        $eventId = $request->get('event_id');

        try {
            $group = Group::create($this->validated_data['group']);

            if ($eventId) {
                $groupId = $group->id;
                EventGroup::firstOrCreate(
                    [
                        'event_id' => $eventId,
                        'group_id' => $groupId,
                    ],
                );
            }

            GroupAddress::create([
                "group_id"      => $group->id,
                'billing'       => 1,
                'name'          => "adresse principale",
                "street_number" => $waGeoData['street_number'] ?? "",
                "route"         => $waGeoData['route'] ?? "",
                "locality"      => $waGeoData['locality'] ?? "",
                "postal_code"   => $waGeoData['postal_code'] ?? "",
                "country_code"  => $waGeoData['country_code'] ?? "",
                "text_address"  => $waGeoData['text_address'] ?? "",
                "lat"           => $waGeoData['lat'] ?? "",
                "lon"           => $waGeoData['lon'] ?? "",
            ]);

            $group->syncSageData();

            $redirect_to = route('panel.groups.edit', $group);

            if (request()->filled('event_id')) {
                $redirect_to = route('panel.manager.event.event_group.index', ['event' => request('event_id')]);
            }

            $this->responseSuccess("Enregistrement effectué");
            $this->redirectTo($redirect_to);
            $this->saveAndRedirect(route('panel.groups.index'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    public function edit(Group $group): Renderable
    {
        return view('groups.edit')
            ->with($this->getGroupEditViewData($group));
    }

    public function getGroupEditViewData(Group $group, ?EventGroup $eventGroup = null): array
    {
        return [
            'data'     => $group,
            'eventId'  => request('event_id'),
            'contacts' => (new GroupAccessor($group))->contacts([
                'eventGroup' => $eventGroup,
                'sortBy'     => 'last_name',
            ]),
        ];
    }


    public function update(Group $group, GroupRequest $request): RedirectResponse
    {
        if ( ! $this->ensureDataIsValid($request, 'group')) {
            return $this->sendResponse();
        }

        try {
            $group->update($this->validated_data['group']);
            $group->syncSageData();
            $this->responseSuccess(__('ui.record_updated'));
            $this->redirect_to = route('panel.groups.edit', $group);
            $this->saveAndRedirect(route('panel.groups.index'));
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->sendResponse();
    }

    /**
     * @throws \Exception
     */
    public function destroy(Group $group): RedirectResponse
    {
        return (new Suppressor($group))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('Le groupe est supprimé.'))
            ->redirectRoute('panel.groups.index')
            ->sendResponse();
    }


}
