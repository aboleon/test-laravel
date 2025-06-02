<?php

namespace App\Http\Controllers\EventManager;

use App\Accessors\Dictionnaries;
use App\Accessors\Places;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\GrantDepositRequest;
use App\Models\Event;
use App\Models\EventManager\Grant\GrantDeposit;
use App\Models\EventManager\Sellable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;
use Throwable;

class GrantDepositController extends Controller
{
    use Responses;

    private GrantDeposit $deposit;


    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event): Renderable
    {
        return view('events.manager.grant.deposit.edit')->with([
            'data' => new GrantDeposit(),
            'event' => $event,
            'route' => route('panel.manager.event.grantdeposit.store', $event),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GrantDepositRequest $request, Event $event): RedirectResponse
    {
       // try {
            $event->grantDeposit()->create($request->validated());

        // Caution
        (new \App\Actions\EventManager\GrantDeposit(event: $event, request: $request))();

        $this->responseSuccess("La caution GRANT a été enregistréе.");
        $this->redirect_to = route('panel.manager.event.grantdeposit.edit', [$event, $event->grantDeposit]);
/*
        } catch (Throwable $e) {
            $this->responseException($e);
        }
*/
        return $this->sendResponse();


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event, GrantDeposit $grantdeposit): Renderable
    {
        return view('events.manager.grant.deposit.edit')->with([
            'data' => $grantdeposit,
            'event' => $event,
            'route' => route('panel.manager.event.grantdeposit.update', [$event, $grantdeposit]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GrantDepositRequest $request, Event $event, GrantDeposit $grantdeposit): RedirectResponse
    {
    //    try {

        $event->grantDeposit->update($request->validated());
        // Caution
        (new \App\Actions\EventManager\GrantDeposit(event: $event, request: $request))();

        $this->redirect_to = route('panel.manager.event.grantdeposit.edit', [$event, $grantdeposit]);

     /*   } catch (Throwable $e) {
            $this->responseException($e);
        }
*/
        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     * @throws \Exception
     */
    public function destroy(Event $event, GrantDeposit $grantdeposit)
    {

        return (new Suppressor($grantdeposit))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('La caution GRANT est supprimée.'))
            ->redirectTo(route('panel.manager.event.deposit.index', $event))
            ->sendResponse();
    }



}
