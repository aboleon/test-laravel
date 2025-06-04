<?php

namespace App\MailTemplates\Traits\Variables;

use App\Accessors\Dictionnaries;
use App\Actions\Front\AutoConnectHelper;
use App\DataTables\View\EventDepositView;
use App\Enum\EventDepositStatus;
use App\Enum\OrderClientType;
use App\Enum\OrderType;
use App\Models\EventManager\Program\EventProgramInterventionOrator;
use App\Models\EventManager\Program\EventProgramSessionModerator;
use App\Models\Order;
use App\Printers\Account;
use MetaFramework\Accessors\Countries;

trait ParticipantVariables
{
    public function PARTICIPANT_DateDeNaissance(): string
    {
        return $this->eventContact?->profile?->birth?->format('d/m/Y') ?? '';
    }

    public function PARTICIPANT_PassportExpiration(): string
    {
        return $this->eventContact?->passports?->first()?->expires_at?->format('d/m/Y') ?: '';
    }

    public function PARTICIPANT_NumDocument(): string
    {
        return $this->eventContact?->passports?->first()?->serial ?: '';
    }

    public function PARTICIPANT_AdresseFacturation(): string
    {
        if ( ! $this->eventContactAddress) {
            return '';
        }

        return Account::address($this->eventContactAddress);
    }

    public function PARTICIPANT_CodePostal(): string
    {
        return $this->eventContactAddress?->postal_code ?? '';
    }

    public function PARTICIPANT_VilleAdresseFacturation(): string
    {
        return $this->eventContactAddress?->locality ?? '';
    }

    public function PARTICIPANT_Email(): string
    {
        return $this->eventContact?->account?->email ?? '';
    }

    public function PARTICIPANT_Fonction(): string
    {
        return $this->eventContact?->profile?->function ?? '';
    }

    public function PARTICIPANT_Nom(): string
    {
        return $this->eventContact?->account?->last_name ?? '';
    }

    public function PARTICIPANT_Participation(): string
    {
        // Check if we have an event contact
        if (!$this->eventContact) {
            return '';
        }

        // Get participation type through the relationship
        return $this->eventContact->participationType?->name ?? '';
    }

    public function PARTICIPANT_Pays(): string
    {
        if ( ! $this->eventContactAddress) {
            return '';
        }

        return Countries::getCountryNameByCode($this->eventContactAddress->country_code);
    }

    public function PARTICIPANT_Prenom(): string
    {
        return $this->eventContact?->account?->first_name ?? '';
    }

    public function PARTICIPANT_Societe(): string
    {
        if ( ! $this->accountAccessor?->isCompany()) {
            return '';
        }

        return $this->eventContact?->profile?->company_name ?? '';
    }

    public function PARTICIPANT_Rpps(): string
    {
        return $this->eventContact?->profile?->rpps ?? '';
    }

    public function PARTICIPANT_Interventions(): string
    {
        $interventions = $this->printInterventions();
        $interventions .= $this->printModerations();

        return $interventions;
    }

    public function PARTICIPANT_Orders(): string
    {
        // Check if we have an event contact
        if ( ! $this->eventContact) {
            return '';
        }

        // Get orders for this event contact
        // Based on the EventContact model, orders are retrieved through the user_id
        $orders = Order::where('client_id', $this->eventContact->user_id)
            ->where('client_type', OrderClientType::CONTACT->value)
            ->where('event_id', $this->eventContact->event_id)
            ->where('type', OrderType::ORDER->value)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return '';
        }

        // Build the output string with date and amount for each order
        $orderDetails = $orders->map(function ($order) {
            $date        = $order->created_at ? $order->created_at->format('d/m/Y') : '';
            $totalAmount = $order->total_net + $order->total_vat; // PriceInteger cast handles formatting

            return $date.' - '.$totalAmount.' €';
        });

        // Join all order details with line breaks
        return $orderDetails->implode('<br>');
    }

    public function PARTICIPANT_Accommodation(): string
    {
        // Check if we have an event contact
        if (!$this->eventContact) {
            return '';
        }

        $accommodationDetails = collect();

        // 1. Get accommodation attributions for this event contact
        $attributions = $this->eventContact->accommodationAttributions();

        if ($attributions->isNotEmpty()) {
            // Group attributions by hotel and date range
            $attributionDetails = $attributions->groupBy(function ($attribution) {
                return $attribution->shoppable->event_hotel_id;
            })->map(function ($hotelGroup) {
                // Get the hotel name from the first attribution in the group
                $hotelName = $hotelGroup->first()->shoppable->eventHotel->hotel->name ?? '';

                // Get date range (min and max dates)
                $dates     = $hotelGroup->pluck('shoppable.date');
                $checkIn   = $dates->min();
                $lastNight = $dates->max();

                // Format dates - checkout is the day after the last night
                $checkInFormatted  = $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d/m/Y') : '';
                $checkOutFormatted = $lastNight ? \Carbon\Carbon::parse($lastNight)->addDay()->format('d/m/Y') : '';

                return $hotelName.' '.$checkInFormatted.'-'.$checkOutFormatted.' (attribution)';
            });

            $accommodationDetails = $accommodationDetails->merge($attributionDetails->values());
        }

        // 2. Get direct accommodation purchases from orders
        $orders = Order::where('client_id', $this->eventContact->user_id)
            ->where('client_type', \App\Enum\OrderClientType::CONTACT->value)
            ->where('event_id', $this->eventContact->event_id)
            ->where('type', \App\Enum\OrderType::ORDER->value)
            ->with(['accommodation.eventHotel.hotel'])
            ->get();

        foreach ($orders as $order) {
            if ($order->accommodation->isNotEmpty()) {
                // Group accommodations by hotel, date range, and cancellation status
                $orderAccommodations = $order->accommodation
                    ->groupBy(function ($accommodation) {
                        // Group by hotel and cancellation status
                        $cancellationKey = '';
                        if ($accommodation->cancelled_at) {
                            $cancellationKey = '_cancelled';
                        } elseif ($accommodation->cancelled_qty > 0 && $accommodation->cancelled_qty < $accommodation->quantity) {
                            $cancellationKey = '_partial';
                        }
                        return $accommodation->event_hotel_id . $cancellationKey;
                    })
                    ->map(function ($hotelGroup) {
                        // Get the hotel name
                        $hotelName = $hotelGroup->first()->eventHotel->hotel->name ?? '';

                        // Get date range (min and max dates)
                        $dates     = $hotelGroup->pluck('date');
                        $checkIn   = $dates->min();
                        $lastNight = $dates->max();

                        // Format dates - checkout is the day after the last night
                        $checkInFormatted  = $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d/m/Y') : '';
                        $checkOutFormatted = $lastNight ? \Carbon\Carbon::parse($lastNight)->addDay()->format('d/m/Y') : '';

                        // Check cancellation status from the first item in the group
                        $firstItem = $hotelGroup->first();
                        $cancellationInfo = '';
                        if ($firstItem->cancelled_at) {
                            $cancellationInfo = ' (annulé)';
                        } elseif ($firstItem->cancelled_qty > 0 && $firstItem->cancelled_qty < $firstItem->quantity) {
                            $cancellationInfo = ' (partiellement annulé)';
                        }

                        return $hotelName.' '.$checkInFormatted.'-'.$checkOutFormatted.$cancellationInfo;
                    });

                $accommodationDetails = $accommodationDetails->merge($orderAccommodations->values());
            }
        }

        // If no accommodations at all, return empty string
        if ($accommodationDetails->isEmpty()) {
            return '';
        }

        // Join all accommodation details with line breaks
        return $accommodationDetails->unique()->implode('<br>');
    }

    public function PARTICIPANT_TransportAllerDateDepart(): string
    {
        return $this->eventContact->transport?->departure_start_date?->format('d/m/Y') ?: '';
    }

    public function PARTICIPANT_TransportAllerHeureDepart(): string
    {
        return $this->eventContact->transport?->departure_start_time?->format('H:i') ?: '';
    }

    public function PARTICIPANT_TransportAllerHeureArrivee(): string
    {
        return $this->eventContact->transport?->departure_end_time?->format('H:i') ?: '';
    }

    public function PARTICIPANT_TransportAllerVilleDepart(): string
    {
        return $this->eventContact->transport?->departure_start_location ?: '';
    }

    public function PARTICIPANT_TransportAllerVilleArrivee(): string
    {
        return $this->eventContact->transport?->departure_end_location ?: '';
    }

    public function PARTICIPANT_TransportAllerTypeTransport(): string
    {
        $type = $this->eventContact->transport?->departure_transport_type;
        if ( ! $type) {
            return '';
        }

        return Dictionnaries::entry('transport', $type)?->name;
    }

    public function PARTICIPANT_TransportRetourDateDepart(): string
    {
        return $this->eventContact->transport?->return_start_date?->format('d/m/Y') ?: '';
    }

    public function PARTICIPANT_TransportRetourHeureDepart(): string
    {
        return $this->eventContact->transport?->return_start_time?->format('H:i') ?: '';
    }

    public function PARTICIPANT_TransportRetourHeureArrivee(): string
    {
        return $this->eventContact->transport?->return_end_time?->format('H:i') ?: '';
    }

    public function PARTICIPANT_TransportRetourVilleDepart(): string
    {
        return $this->eventContact->transport?->return_start_location ?: '';
    }

    public function PARTICIPANT_TransportRetourVilleArrivee(): string
    {
        return $this->eventContact->transport?->return_end_location ?: '';
    }

    public function PARTICIPANT_TransportRetourTypeTransport(): string
    {
        $type = $this->eventContact->transport?->return_transport_type;
        if ( ! $type) {
            return '';
        }

        return Dictionnaries::entry('transport', $type)?->name;
    }

    public function PARTICIPANT_Deposits(): string
    {
        // Check if we have an event contact
        if ( ! $this->eventContact) {
            return '';
        }

        // Get deposits from EventDepositView for this event contact
        $deposits = EventDepositView::where('event_contact_id', $this->eventContact->id)
            ->where('event_id', $this->eventContact->event_id)
            ->get();

        // If no deposits, return empty string
        if ($deposits->isEmpty()) {
            return '';
        }

        // Build the output string with amount, status and type for each deposit
        $depositDetails = $deposits->map(function ($deposit) {
            // Get amount (total_ttc has the total amount)
            $amount = $deposit->total_ttc.' €';
            $depositType = ($deposit->shoppable_type === 'grantdeposit' ? __('front/ui.grantdeposit') : __('front/ui.servicedeposit'));

            return $depositType . ' ' .$amount.' - '. EventDepositStatus::translated($deposit->status);
        });

        // Join all deposit details with line breaks
        return $depositDetails->implode('<br>');
    }

    public function PARTICIPANT_Services(): string
    {
        // Check if we have an event contact
        if (!$this->eventContact) {
            return '';
        }

        $serviceDetails = collect();

        // 1. Get service attributions for this event contact
        $attributions = $this->eventContact->serviceAttributions()
            ->with('service')
            ->get();

        if ($attributions->isNotEmpty()) {
            $attributionDetails = $attributions->map(function ($attribution) {
                $service = $attribution->service;
                if (!$service) {
                    return null;
                }

                $title = $service->title ?? '';
                $serviceDate = $service->service_date ?? '';
                $description = $service->description ?? '';
                $price = $service->price . ' €';

                $dateInfo = $serviceDate ? ' ' . $serviceDate : '';
                return [
                    'text' => $title . $dateInfo . ' - ' . $price . ' - ' . $description . ' (attribution)',
                    'date' => $service->service_date
                ];
            })->filter();

            $serviceDetails = $serviceDetails->merge($attributionDetails);
        }

        // 2. Get direct service purchases from orders
        $orders = Order::where('client_id', $this->eventContact->user_id)
            ->where('client_type', \App\Enum\OrderClientType::CONTACT->value)
            ->where('event_id', $this->eventContact->event_id)
            ->where('type', \App\Enum\OrderType::ORDER->value)
            ->with(['services.service'])
            ->get();

        foreach ($orders as $order) {
            if ($order->services->isNotEmpty()) {
                $orderServices = $order->services->map(function ($cartService) {
                    $service = $cartService->service;
                    if (!$service) {
                        return null;
                    }

                    $title = $service->title ?? '';
                    $serviceDate = $service->service_date ?? '';
                    $description = $service->description ?? '';
                    $price = $cartService->unit_price . ' €';

                    // Check cancellation status
                    $cancellationInfo = '';
                    if ($cartService->cancelled_at) {
                        // If cancelled_at is not null, it's cancelled
                        $cancellationInfo = ' (annulé)';
                    } elseif ($cartService->cancelled_qty > 0 && $cartService->cancelled_qty < $cartService->quantity) {
                        // If cancelled_at is null but there's a partial cancellation
                        $cancellationInfo = ' (partiellement annulé)';
                    }

                    $dateInfo = $serviceDate ? ' ' . $serviceDate : '';
                    return [
                        'text' => $title . $dateInfo . ' - ' . $price . ' - ' . $description . $cancellationInfo,
                        'date' => $service->service_date
                    ];
                })->filter();

                $serviceDetails = $serviceDetails->merge($orderServices);
            }
        }

        // If no services at all, return empty string
        if ($serviceDetails->isEmpty()) {
            return '';
        }

        // Sort by service_date and extract text
        $sortedServices = $serviceDetails->sortBy('date')->pluck('text');

        // Join all service details with line breaks
        return $sortedServices->unique()->implode('<br>');
    }

    public function PARTICIPANT_Pec(): string
    {
        // Check if we have an event contact
        if (!$this->eventContact) {
            return '';
        }

        // Get distinct grant_ids from PecDistribution for this event contact
        $pecDistributions = \App\Models\PecDistribution::where('event_contact_id', $this->eventContact->id)
            ->distinct('grant_id')
            ->with('grant')
            ->get();

        // If no PEC distributions, return empty string
        if ($pecDistributions->isEmpty()) {
            return '';
        }

        // Get unique grants and their titles
        $grantTitles = $pecDistributions->map(function ($distribution) {
            return $distribution->grant->title ?? '';
        })->filter()->unique();

        // Join all grant titles with line breaks
        return $grantTitles->implode('<br>');
    }

    public function PARTICIPANT_Phone(): string
    {
        // Check if we have an event contact
        if (!$this->eventContact) {
            return '';
        }

        // Get account from event contact
        $account = $this->eventContact->account;
        if (!$account) {
            return '';
        }

        // Get phones from account
        $phones = $account->phones;
        if ($phones->isEmpty()) {
            return '';
        }

        // Format phones as "name phone" per line
        $phoneDetails = $phones->map(function ($phone) {
            $name = $phone->name ?? '';
            $phoneNumber = $phone->phone ?? '';

            return trim($name . ' ' . $phoneNumber);
        })->filter();

        // Join all phones with line breaks
        return $phoneDetails->implode('<br>');
    }

    public function PARTICIPANT_Titre(): string
    {
        return $this->eventContact?->profile?->title?->name ?? '';
    }

    public function PARTICIPANT_UrlConnect(): string
    {
        if ( ! $this->eventContact) {
            return '';
        }

        $token = AutoConnectHelper::generateAutoConnectUrlForEventContact($this->eventContact);

        return '<a href="'.$token.'">'.__('ui.auto_connect_link').'</a>';
    }
    private function printInterventions(): string
    {
        // Check if we have an event contact
        if ( ! $this->eventContact) {
            return '';
        }

        // Get interventions for this event contact
        $interventions = EventProgramInterventionOrator::where('events_contacts_id', $this->eventContact->id)
            ->with([
                'intervention.session.room.place',
                'intervention.specificity',
                'intervention.orators.user',
            ])
            ->get();

        // If no interventions, return empty string
        if ($interventions->isEmpty()) {
            return '';
        }

        // Build the output for each intervention
        $interventionDetails = $interventions->map(function ($interventionOrator) {
            $intervention = $interventionOrator->intervention;
            if ( ! $intervention) {
                return null;
            }

            $session = $intervention->session;

            // Extract date and time from start datetime
            $startDate = $intervention->start ? $intervention->start->format('d/m/Y') : '';
            $startTime = $intervention->start ? $intervention->start->format('H:i') : '';

            // Calculate duration in minutes
            $duration = '';
            if ($intervention->start && $intervention->end) {
                $durationMinutes = $intervention->start->diffInMinutes($intervention->end);
                $duration        = $durationMinutes.'mn';
            }

            // Get intervention type
            $interventionType = $intervention->specificity->name ?? '';

            // Build first line: [JJ/MM/AAAA] [16:00] [8mn] [Type intervention]
            $firstLine = '['.$startDate.'] ['.$startTime.'] ['.$duration.'] ['.$interventionType.']';

            // Get session name
            $sessionName = $session->name ?? '';

            // Get intervention title
            $interventionTitle = $intervention->name ?? '';

            // Get room information
            $roomName = '';
            if ($session->room) {
                $placeName = $session->room->place->name ?? '';
                $roomName  = $session->room->name ?? '';
                $roomName  = $placeName.' - '.$roomName;
            }

            // Get co-orators (excluding current event contact)
            $coOrators = $intervention->orators
                ->where('id', '!=', $this->eventContact->id)
                ->map(function ($orator) {
                    if ($orator->user) {
                        return $orator->user->first_name.' '.$orator->user->last_name;
                    }

                    return null;
                })
                ->filter()
                ->implode(', ');

            // Build the complete intervention detail
            $details = "<p>".$firstLine."<br>";
            $details .= $sessionName."<br>";
            $details .= $interventionTitle."<br>";
            $details .= $roomName."<br>";
            if ($coOrators) {
                $details .= $coOrators;
            }
            $details .= "</p>";

            return $details;
        })->filter();

        // Join all intervention details with line breaks
        return $interventionDetails->implode("");
    }

    private function printModerations(): string
    {
        // Check if we have an event contact
        if ( ! $this->eventContact) {
            return '';
        }

        // Get moderations for this event contact
        $moderations = EventProgramSessionModerator::where('events_contacts_id', $this->eventContact->id)
            ->with([
                'session.room.place',
                'session.dayRoom.day',
                'moderatorType',
                'session.moderators.user',
            ])
            ->get();

        // If no moderations, return empty string
        if ($moderations->isEmpty()) {
            return '';
        }

        // Build the output for each moderation
        $moderationDetails = $moderations->map(function ($sessionModerator) {
            $session = $sessionModerator->session;
            if ( ! $session) {
                return null;
            }

            // Get session start time from day and session duration
            $sessionDay = $session->dayRoom->day ?? null;
            $startDate  = '';
            $startTime  = '';
            $duration   = '';

            if ($sessionDay && $sessionDay->date) {
                // Parse the day date and combine with session start time if available
                $startDate = \Carbon\Carbon::parse($sessionDay->date)->format('d/m/Y');

                // You might need to calculate start time based on position and previous sessions
                // For now, using a placeholder approach
                $startTime = '00:00'; // This would need proper calculation

                // Session duration
                if ($session->duration) {
                    $duration = $session->duration.'mn';
                }
            }

            // Get moderator type
            $moderatorType = $sessionModerator->moderatorType->name ?? '';

            // Build first line: [JJ/MM/AAAA] [16:00] [8mn] [Type modérateur]
            $firstLine = '['.$startDate.'] ['.$startTime.'] ['.$duration.'] ['.$moderatorType.']';

            // Get session name
            $sessionName = $session->name ?? '';

            // Get room information
            $roomName = '';
            if ($session->room) {
                $placeName = $session->room->place->name ?? '';
                $roomName  = $session->room->name ?? '';
                $roomName  = $placeName.' - '.$roomName;
            }

            // Get co-moderators (excluding current event contact)
            $coModerators = $session->moderators
                ->where('events_contacts_id', '!=', $this->eventContact->id)
                ->map(function ($moderator) {
                    if ($moderator->user) {
                        return $moderator->user->first_name.' '.$moderator->user->last_name;
                    }

                    return null;
                })
                ->filter()
                ->implode(', ');

            // Build the complete moderation detail
            $details = '<p>'.$firstLine."<br>";
            $details .= $sessionName."<br>";
            $details .= $roomName."<br>";
            if ($coModerators) {
                $details .= $coModerators;
            }
            $details .= "</p>";

            return $details;
        })->filter();

        // Join all moderation details with line breaks
        return $moderationDetails->implode("");
    }

}
