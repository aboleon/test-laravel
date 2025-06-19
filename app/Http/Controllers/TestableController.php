<?php

namespace App\Http\Controllers;


use App\Accessors\Accounts;
use App\Accessors\EventContactAccessor;
use App\Models\Account;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramSession;
use App\Services\Filters\EventContactFilter;
use App\Traits\EventCommons;
use Illuminate\Support\LazyCollection;
use Mediaclass;
use MetaFramework\Accessors\Countries;
use MetaFramework\Services\Validation\ValidationTrait;
use MetaFramework\Traits\DateManipulator;

class TestableController extends Controller
{
    use ValidationTrait;
    use DateManipulator;
    use EventCommons;

    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function index()
    {
        $ec = EventContact::find(394);

        d($ec->grantDeposit);

        $a = new EventContactAccessor()->setEventContact($ec);
        d(
           $a->isExemptGrantFromDeposit()
        );
        d(
           $a->hasPaidGrantDeposit()
        );
    }

    public function search()
    {
        $searchFilter
            = [
            'rules' => [
                [
                    'condition' => 'AND',
                    'rules'     => [
                        [
                            'id'       => 'users.first_name',
                            'operator' => 'equal',
                            'value'    => 'John',
                        ],
                        [
                            'id'       => 'users.last_name',
                            'operator' => 'equal',
                            'value'    => 'Doe',
                        ],
                    ],
                ],
                [
                    'condition' => 'OR',
                    'rules'     => [
                        [
                            'id'       => 'users.email',
                            'operator' => 'equal',
                            'value'    => 'john@doe.com',
                        ],
                    ],
                ],
                [
                    'condition' => 'OR',
                    'rules'     => [
                        [
                            'id'       => 'users.email',
                            'operator' => 'equal',
                            'value'    => 'juju59.kiki13@gmail.com',
                        ],
                    ],
                ],
            ],
        ];

        $eventId = 44;
        $filter  = new EventContactFilter();
        $query   = $filter->buildQuery($searchFilter, $eventId);


        $data = $filter->getFilteredContactIds($searchFilter, $eventId);

        return view('testable', ['data' => $data]);
    }

    public function deleteOrders()
    {
        $ec  = EventContact::find(378);
        $ecc = (new EventContactAccessor())->setEventContact($ec);

        d($ec->event_id);
        d($ec->orders()->where('event_id', $ec->event_id)->get());
        d($ecc->hasAnyOrders(), 'hasAnyOrders');

        $event = Event::find(42);
        d($event->contacts()->whereHas('orders', fn($q) => $q->where('event_id', $event->id))->count(), 'ddd');


        $a = EventContact::find(378);

        $a->orders;

        d($a, 'dld');
    }

    public function testMedia()
    {
        $mediaclass = new Mediaclass;
        $account    = Account::find(245);
        $media      = $mediaclass
            ->forModel($account, 'avatar')  // Specify model and group
            ->single()                        // Indicate you want a single image
            ->first();
        d($media);

        d(mediaclass_url($media));

        d(Accounts::getPhotoByAccount($account));
    }

    public function testTransportGrant()
    {
        $transport = EventTransport::find(65);


        $eventContactAccessor = (new EventContactAccessor())->setEventContact($transport->eventContact);
        // Fetch Grant
        if ($eventContactAccessor->isPecAuthorized()) {
            $pec = (new PecActions());

            $pec->setEventContact($transport->eventContact);
            $pec->setEvent($transport->eventContact->event);

            $pec->pecParser();
            if ($pec->getPecParser()->hasGrants($pec->getEventContact()->id)) {
                $pec->pecFinder();

                $pecFinder = (new PecFinder())
                    ->setEventContact($pec->getEventContact())
                    ->setTransportFeesWhitoutTax(0)
                    ->setTransportFeesWithTax(0)
                    ->setGrants($pec->getPecParser()->getGrantsFor($pec->getEventContact()->id));

                $pec->setPecDistributionResult($pecFinder->filterGrants());

                if ($pec->getPecDistributionResult()->isCovered()) {
                    $grant                       = $pec->getPecDistributionResult()->getDistribution();
                    $transport->price_before_tax = $grant['transport']['cost_max'];
                    $transport->price_after_tax  = $grant['transport']['cost_max'];
                    $transport->save();
                    //$this->responseElement('grant', $grant);
                } /* else {
                    $this->responseWarning("Aucun financement grant n'est disponible");
                }
              */
            }
        }

        de($this->fetchResponse());
    }

    public function eventContact()
    {
        $id       = 140;
        $accessor = (new EventContactAccessor())->setEventContact($id);

        d($accessor->getOrdersWithRemainingPayments());
        d($accessor->getModel(), 'ModelActions');
        d($accessor->getOrders(), 'Orders');
        d($accessor->getAssignedOrdersWithRemainingPayments(), 'Assigned Orders');
    }

    public function testReimbursement()
    {
        request()->merge(
            input: [
                'id' => 75,
            ],
        );

        d(
            (new ReimburseEventDepositAction())->reimburseEventDeposit(),
        );
    }


    public function testAvailability()
    {
        request()->merge(
            input: [
                'accommodation_id'   => 55,
                // 'account_type'     => 'group',
                'event_contact_id'   => 407,
                'participation_type' => 6,
                //'event_group_id'   => 53,
                'date'               => '2025-03-03',
                'room_group_id'      => 68,
                //  'entry_date' => '03/03/2025',
                //  'out_date' => '04/04/2025',
            ],
        );
        // de(request()->all());


        //  $eventContact = EventContactAccessor::getEventContactByEventAndUser($accommodation->event, request('account_id'));
        /*
                $this->availability = (new Availability())
                    ->setEventAccommodation(request('event_accommodation_id'))
                    ->setDate(request('date'))
                    ->setRoomGroupId((int)request('shoppable_id'))
                    ->setParticipationType((int)request('participation_type'))
                    ->setEventGroupId(request('account_type') == 'group' ? (int)request('event_group_id') : 0);
                */

        $availability = (new Availability())
            ->setEventAccommodation(request('accommodation_id'))
            ->setEventContact(request('event_contact_id'))
            //  ->setDateRange([('entry_date'), request('out_date')])
            ->setParticipationType((int)request('participation_type'))
            //->setDate((string)request('date'))
            // ->setEventGroupId(request('account_type') == 'group' ? (int)request('event_group_id') : 0)
            //->setExcludeRoomsId(24)
            //->setDateRange([request('entry_date'), request('out_date')])
            // ->publishedRoomsOnly()
            //   ->setRoomGroupId((int)request('room_group_id'))
        ;


        d($availability->getAvailability(), 'Final availability');

        $bookings = (new AvailabilityRecap($availability));

        $bookings_data = $bookings->get('2025-03-03', 68);
        d($bookings_data, 'AvailabilityRecap');

        // d($availability->accountIsGrantable());

        // $ec = $availability->getEventContact();
        // d($ec->hasPaidGrantDeposit(), 'hasPaidGrantDeposit');
        // d($availability->getEventContact());


        // d($availability->get('contingent'), 'contingent');
        // d($availability->getEventGroupId(),'GroupId');
        //d($availability->baseGroupId(),'Base GroupId');
        //   d($availability->get('participation_type'), 'participation_type');
        //   d($availability->getAvailability(), 'Availability for ptype 11');
        //   d($availability->get('blocked'), 'Blocked for 11');
        // d($availability->accountIsGrantable(), 'accountIsGrantable');
        // d($availability->getGrantDistributedDetail(), 'getGrantDistributedDetail');
        // d($availability->getGrantDistributedDetail(), 'getGrantDistributedDetail');
        //d($availability->getGrantDistributed(), 'getGrantDistributed');
        //d($availability->getRoomGroupAvailability(), 'GetRoomGroup');
        // d($availability->getSummarizedData(), 'Summarized data');
        //d($availability->getRoomGroups(), 'RoomGroups');
        //d($availability->getRoomGroupAvailability(), 'getRoomGroupAvailability');
        // d($availability->getRoomConfigs(), 'Room Configs');


    }

    private
    function decreaseAccommodationStock()
    {
        request()->merge([
            "action"                      => "decreaseAccommodationStock",
            "callback"                    => "resetAccommodationCartSelectablesStock",
            "shoppable_model"             => "App\\Models\\EventManager\\AccommodationAccessor\\RoomGroup",
            "shoppable_id"                => "21",
            "event_accommodation_id"      => "15",
            "room_id"                     => "24",
            "prevalue"                    => "1",
            "order_uuid"                  => "13552e97-eff5-4f1d-bc23-e4b5ca2d14dc",
            "quantity"                    => "2",
            "cart_id"                     => 201,
            'date'                        => '2024-03-20',
            "account_type"                => "contact",
            "account_id"                  => "109",
            "row_id"                      => "7zi09ywp0",
            'participation_type'          => 6,
            "shopping_cart_accommodation" => [
                "2024-03-20" => [
                    "date"           => [
                        "2024-03-20",
                    ],
                    "room_id"        => [
                        "26",
                    ],
                    "room_group_id"  => [
                        "21",
                    ],
                    "quantity"       => [
                        "1",
                    ],
                    "unit_price"     => [
                        "130",
                    ],
                    "price"          => [
                        "130",
                    ],
                    "price_ht"       => [
                        "108.33",
                    ],
                    "vat"            => [
                        "21.67",
                    ],
                    "event_hotel_id" => [
                        "15",
                    ],
                ],
            ],
        ]);


        $availability = (new Availability())
            ->setEventAccommodation(request('event_accommodation_id'))
            ->setDate(request('date'))
            ->setRoomGroupId((int)request('shoppable_id'))
            ->setParticipationType((int)request('participation_type'))
            ->setEventGroupId(request('account_type') == 'group' ? (int)request('account_id') : 0);


        d($availability->get('contingent'), 'contingent');
        d($availability->getSummarizedData(), 'Summarized data');
        d($availability->getRoomGroups(), 'RoomGroups');
        d($availability->getRoomConfigs(), 'Room Configs');
        d($availability->getAvailability(), 'Availability');
        /*
          d(
              (new ContingentActions())->decreaseStock(), 'decreaseAccommodationStock'
          );
        */
    }


    public
    function fetchAccommodationForEvent()
    {
        request()->merge([
            'action'             => 'fetchAccommodationForEvent',
            'callback'           => 'showAccommodatioRecap',
            'event_hotel_id'     => 26,
            'entry_date'         => '12/06/2024',
            'out_date'           => '15/06/2024',
            'account_type'       => 'contact',
            'pec'                => 1,
            'account_id'         => 55,
            'event_group_id'     => 0,
            "participation_type" => 4,
        ]);

        d(
            (new \App\Actions\Order\OrderAccommodationActions())->fetchAccommodationForEvent(),
            'fetchAccommodationForEvent',
        );
    }

    private
    function testPecParser()
    {/*
        $order = Order::find(271);

        $orderPecCount = $order->pecDistributions->count();

        de($order->pecDistributions->first()->type == PecType::PROCESSING_FEE->value);
        if ($orderPecCount == 0) {
            $order->pecQuota()->delete();
        } else {
            if ($orderPecCount < 2 && $order->pecDistributions->first()->type == PecType::PROCESSING_FEE) {
                $order->pecDistributions()->delete();
                $order->pecQuota()->delete();
            }
        }

        exit;
*/
        $contact_id = 329;
        $contact    = EventContact::find($contact_id);

        $pec = new PecParser($contact->event, collect()->push($contact));
        $pec->trackFailures();
        $pec->calculate();

        d($pec->grantParser->fetchAvailableGrants(), 'fetchAvailableGrants');
        /*
        if ($pec->hasGrants($contact_id)) {
            $pecFinder = new PecFinder();
            $pecFinder->setEventContact($contact);
            $pecFinder->setServices([15 => 800, 16 => 500]);
            $pecFinder->setGrants($pec->getGrantsFor($contact_id));
            //$pecFinder->setAccommodationTotal(500);//OrderRequestAccessor::getTotalAccommodationPecFromRequest()
            $pecFinder->askForProcessingFees(true);


            $pecDistrubutionResult = $pecFinder->filterGrants();

            d($pecDistrubutionResult, 'filterGrants of Pec Finder');
        }
        */
    }


    private
    function fullPecOrderTest()
    {
        //$event = Event::find(75);//->load('contacts.profile','contacts.address');
        $contact_id = 163;
        $contact    = EventContact::find($contact_id);


        //$pec = new PecParser($event, $event->contacts);
        $pec = new PecParser($contact->event, collect()->push($contact));
        $pec->trackFailures();
        $pec->calculate();

        d($pec->grantParser->fetchAvailableGrants(), 'fetchAvailableGrants');

        d($pec->hasGrants($contact_id), 'hasGrants');
        d($pec->getEligibilitySummary(), 'getEligibilitySummary');
        //d($pec->getEligibilityFailures(), 'getEligibilityFailures');
        //d($pec->getEligibleGrants(),'getEligibleGrants');
        //d($pec->getGrantsFor($contact_id), 'getGrantsFor');


        if ($pec->hasGrants($contact_id)) {
            $pecFinder = new PecFinder();
            $pecFinder->setEventContact($contact);
            $pecFinder->setServices([56 => 500]);
            $pecFinder->setGrants($pec->getGrantsFor($contact_id));
            //$pecFinder->setAccommodationTotal(500);//OrderRequestAccessor::getTotalAccommodationPecFromRequest()
            $pecFinder->askForProcessingFees(true);


            $pecDistrubutionResult = $pecFinder->filterGrants();

            //  d($pecDistrubutionResult, 'filterGrants of Pec Finder');

            // de($pecDistrubutionResult->getProcessingFee(),'getProcessingFee');

            de($pec->getEligibilityFor($contact_id, 33), 'getEligibilityFor');

            $actions = (new PecActions());
            $actions->setEventContact($contact);
            $actions->setEvent($contact->event);
            // $actions->setOrder(Order::find(246));
            $actions->setPecParser($pec);
            $actions->setPecDistributionResult($pecDistrubutionResult);

            d($actions->quotaMatches());

            $actions->registerPecDistributionResult();
            $actions->registerQuotas();

            d($actions->fetchResponse());
        }
    }
}

