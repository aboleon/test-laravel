<?php

namespace App\Http\Controllers\EventManager\Transport;

use App\Accessors\EventContactAccessor;
use App\Accessors\EventManager\TransportAccessor;
use App\DataTables\EventTransportDesiredManagementDataTable;
use App\DataTables\EventTransportUndesiredManagementDataTable;
use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventManager\Transport\EventTransportRequest;
use App\Mail\EventManager\Transport\TransportDepartureInfoReady;
use App\Mail\EventManager\Transport\TransportReturnInfoReady;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Transport\EventTransport;
use App\Traits\Models\EventTransportModelTrait;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use MetaFramework\Actions\Suppressor;
use MetaFramework\Traits\Responses;

class TransportController extends Controller
{
    use Responses;
    use EventTransportModelTrait;

    private EventTransport $transport;

    /**
     * Display a listing of the resource.
     */
    public function index(Event $event)
    {
        $desiredDataTable   = new EventTransportDesiredManagementDataTable($event);
        $undesiredDataTable = new EventTransportUndesiredManagementDataTable($event);

        $totals = EventTransport::selectRaw(
            '
    SUM(CASE
        WHEN price_before_tax IS NOT NULL THEN price_before_tax
        WHEN price_before_tax IS NULL AND price_after_tax IS NOT NULL THEN price_after_tax
        ELSE max_reimbursement
    END) as total_before_tax,
    SUM(CASE
        WHEN price_after_tax IS NOT NULL THEN price_after_tax
        WHEN price_before_tax IS NOT NULL AND price_after_tax IS NULL THEN price_before_tax
        ELSE max_reimbursement
    END) as total_after_tax',
        )
            ->where('events_contacts.event_id', '=', $event->id)
            ->join('events_contacts', 'events_contacts.id', '=', 'event_transports.events_contacts_id')
            ->first();


        $totals['total_before_tax'] /= 100;
        $totals['total_after_tax']  /= 100;


        return view('events.manager.transport.index', [
            'totals'              => $totals,
            'event'               => $event,
            'desired_dataTable'   => $desiredDataTable->html(),
            'undesired_dataTable' => $undesiredDataTable->html(),
        ]);
    }

    public function desiredData(Event $event)
    {
        $dataTable = new EventTransportDesiredManagementDataTable($event);

        return $dataTable->ajax();
    }

    public function undesiredData(Event $event)
    {
        $dataTable = new EventTransportUndesiredManagementDataTable($event);

        return $dataTable->ajax();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Event $event): Renderable
    {
       $eventContact = EventContact::find((int)request('event_contact'));

        return $this->getEditView($event, $eventContact);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editByEventContact(Event $event, EventContact $eventContact): Factory|View
    {
        return $this->getEditView($event, $eventContact, $eventContact->transport);
    }

    public function edit(Event $event, EventTransport $transport): Factory|View
    {
        return $this->getEditView($event, $transport->eventContact, $transport);
    }


    public function updateByEventContact(EventTransportRequest $request, Event $event, EventContact $eventContact)
    {
        // try {
        $this->transport = $eventContact->transport ?: new EventTransport();
        $this->process($request);
        $this->redirect_to = route('panel.manager.event.transport.edit', ['event' => $event, 'transport' => $this->transport]);
        $this->responseSuccess("Le transport a bien été enregistré");

        /*   } catch (Throwable $e) {
               $this->responseException($e);
           } */

        return $this->sendResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Exception
     */
    public function destroy(Event $event, EventTransport $transport)
    {
        return (new Suppressor($transport))
            ->remove()
            ->whitout('object')
            ->responseSuccess(__('transport.messages.deleted'))
            ->redirectTo(route('panel.manager.event.transport.index', $event))
            ->sendResponse();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function process(Request $request)
    {
        $item           = $request->input('item.main');
        $accountData    = $request->input('item.account');
        $eventContactId = (int)$item['event_contact_id'];
        $participant    = EventContact::find($eventContactId);
        if ( ! $participant) {
            $this->responseError("Participant introuvable");
        }

        // account profile
        $account                             = $participant->account;
        $accountProfile                      = $account->profile;
        $accountProfile->passport_first_name = $accountData['passport_first_name'];
        $accountProfile->passport_last_name  = $accountData['passport_last_name'];
        $accountProfile->birth               = DateHelper::appDateToSqlDate($accountData['birth_date']);
        $accountProfile->save();

        // media


        // other fields
        $this->transport->events_contacts_id = $eventContactId;

        $this->transport->departure_online                     = $item['departure_online'] ?? null;
        $this->transport->departure_step                       = $item['departure_step'];
        $this->transport->departure_transport_type             = $item['departure_transport_type'];
        $this->transport->departure_start_date                 = DateHelper::appDateToSqlDate($item['departure_start_date']);
        $this->transport->departure_start_time                 = $item['departure_start_time'];
        $this->transport->departure_start_location             = $item['departure_start_location'];
        $this->transport->departure_end_time                   = $item['departure_end_time'];
        $this->transport->departure_end_location               = $item['departure_end_location'];
        $this->transport->departure_reference_info_participant = $item['departure_reference_info_participant'];
        $this->transport->departure_participant_comment        = $item['departure_participant_comment'];

        $this->transport->return_online                     = $item['return_online'] ?? null;
        $this->transport->return_step                       = $item['return_step'];
        $this->transport->return_transport_type             = $item['return_transport_type'];
        $this->transport->return_start_date                 = DateHelper::appDateToSqlDate($item['return_start_date']);
        $this->transport->return_start_time                 = $item['return_start_time'];
        $this->transport->return_start_location             = $item['return_start_location'];
        $this->transport->return_end_time                   = $item['return_end_time'];
        $this->transport->return_end_location               = $item['return_end_location'];
        $this->transport->return_reference_info_participant = $item['return_reference_info_participant'];
        $this->transport->return_participant_comment        = $item['return_participant_comment'];

        $this->transport->transfer_shuttle_time_departure = $item['transfer_shuttle_time_departure'] ?? null;
        $this->transport->transfer_shuttle_time_return    = $item['transfer_shuttle_time_return'] ?? null;
        $this->transport->transfer_info_departure         = $item['transfer_info_departure'] ?? null;
        $this->transport->transfer_info_return            = $item['transfer_info_return'] ?? null;
        $this->transport->travel_preferences              = $item['travel_preferences'];
        $this->transport->price_before_tax                = $item['price_before_tax'] ?? 0;
        $this->transport->price_after_tax                 = $item['price_after_tax'] ?? 0;
        $this->transport->max_reimbursement               = $item['max_reimbursement'] ?? 0;
        $this->transport->admin_comment                   = $item['admin_comment'] ?? null;

        // changement de mode de gestion
        if ($item['desired_management'] != $this->transport->desired_management) {
            $this->transport->management_history = $this->transport->desired_management;
            $this->transport->management_mail    = null;
        }
        $this->transport->desired_management = $item['desired_management'];


        $this->transport->save();


        // sending mails
        if ($this->transport?->departure_online) {
            Mail::to($participant->user->email)
                ->send(new TransportDepartureInfoReady($this->transport));
        }

        if ($this->transport->return_online) {
            Mail::to($participant->user->email)
                ->send(new TransportReturnInfoReady($this->transport));
        }
    }


    private function getEditView(Event $event, ?EventContact $eventContact = null, ?EventTransport $transport = null): Factory|View
    {
        $route      = null;
        $formMethod = null;

        $eventContactAccessor = (new EventContactAccessor());

        if ($eventContact) {
            $route      = route('panel.manager.event.transport.updateByEventContact', [$event, $eventContact]);
            $formMethod = 'PUT';
            $eventContactAccessor->setEventContact($eventContact);
        }

        return view('events.manager.transport.edit')->with([
            'transport'            => $transport,
            'transportAccessor'    => (new TransportAccessor())->setEventTransport($transport),
            'eventContact'         => $eventContact,
            'event'                => $event,
            'route'                => $route,
            'formMethod'           => $formMethod,
            'eventContactAccessor' => $eventContactAccessor,
        ]);
    }
}
