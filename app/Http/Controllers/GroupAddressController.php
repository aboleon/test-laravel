<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupAddressRequest;
use App\Models\Group;
use App\Models\GroupAddress;
use App\Traits\ValidationAddress;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class GroupAddressController extends Controller
{
    use ValidationAddress;

    public function create(Group $group): Renderable
    {
        return view('address.group')->with([
            'route' => route('panel.groups.addresses.store', $group),
            'title' => 'Ajouter une addresse pour ' . $group->name,
            'group' => $group,
            'data' => new GroupAddress(),
            'group_route' => 'panel.groups',
        ]);
    }

    public function edit(Group $group, GroupAddress $address): Renderable
    {
        return view('address.group')->with([
            'data' => $address,
            'route' => route('panel.groups.addresses.update', [$group, $address]),
            'method' => 'put',
            'title' => 'Éditer une addresse',
            'group' => $group,
            'group_route' => 'panel.groups',
        ]);
    }

    public function store(Group $group, GroupAddressRequest $request): RedirectResponse
    {
        if (!$this->ensureDataIsValid($request)) {
            return $this->sendResponse();
        }

        DB::beginTransaction(); // Start a database transaction

        try {
            // If the new address has billing=1 or there's no address with billing=1 for the group
            // we want to ensure that only this address has billing=1 and all others have billing=0.
            if (($this->validated_data['billing'] ?? null) == 1 || !$group->address()->where('billing', 1)->exists()) {
                $group->address()->update(['billing' => null]); // no-op if the group has no address
                $this->validated_data['billing'] = 1;
            }

            $group->address()->save(new GroupAddress($this->validated_data));
            $this->responseSuccess("L'adresse est ajoutée.");
            $this->redirect_to = route('panel.groups.edit', $group);

            DB::commit(); // Commit the transaction if all is well

        } catch (Throwable $e) {
            DB::rollback(); // Rollback the transaction in case of error
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        }
    }

    public function update(Group $group, GroupAddress $address, GroupAddressRequest $request): RedirectResponse
    {
        if (!$this->ensureDataIsValid($request)) {
            return $this->sendResponse();
        }

        DB::beginTransaction(); // Start a database transaction

        try {
            // If the address is being updated to have billing=1, ensure all other addresses are set to billing=0
            if (($this->validated_data['billing'] ?? null) == 1) {
                $group->address()->where('id', '!=', $address->id)->update(['billing' => null]);
            }
            // If the address currently has billing=1 and is being edited to have billing=0,
            // set another address (if exists) to billing=1
            elseif ($address->billing == 1 && ($this->validated_data['billing'] ?? null) == 0) {
                $nextDefaultAddress = $group->address()->where('id', '!=', $address->id)->first();
                if ($nextDefaultAddress) {
                    $nextDefaultAddress->update(['billing' => 1]);
                } else {
                    // Handle the case where there are no other addresses to set as default.
                    // You can/could warn the user or handle as appropriate for your application.
//                    $this->responseWarning("There's no other address to set as default.");
                }
            }

            $address->update($this->validated_data);
            $this->responseSuccess("L'adresse est mise à jour.");

            DB::commit(); // Commit the transaction if all is well

        } catch (Throwable $e) {
            DB::rollback(); // Rollback the transaction in case of error
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        }
    }


    public function destroy(Group $group, GroupAddress $address): RedirectResponse
    {
        DB::beginTransaction(); // Start a database transaction

        try {
            // If the address being deleted is the default
            if ($address->billing == 1) {
                // Choose another address to set as default
                $nextDefaultAddress = $group->address()->where('id', '!=', $address->id)->first();

                // If another address exists, set it as default, otherwise warn the user.
                if ($nextDefaultAddress) {
                    $nextDefaultAddress->update(['billing' => 1]);
                } else {
//                    $this->responseWarning("The deleted address was the default. There's no other address to set as default now.");
                }
            }

            $address->delete();
            $this->responseSuccess("L'adresse a été supprimée.");

            DB::commit(); // Commit the transaction if all is well

        } catch (Throwable $e) {
            DB::rollback(); // Rollback the transaction in case of error
            $this->responseException($e);
        } finally {
            return $this->sendResponse();
        }
    }

}
