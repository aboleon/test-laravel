<?php

namespace App\Accessors\EventManager;

use App\Accessors\EventManager\Sellable\Deposits;
use App\Accessors\Front\FrontCartAccessor;
use App\DataTables\View\EventSellableServiceStockView;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Sellable;
use App\Services\NestedCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SellableAccessor
{

    public function __construct(private readonly Sellable $sellable) {}

    public function getBookingsSubCounts(): array
    {
        return [
            'bookings'   => $this->sellable->bookings()->sum("quantity"),
            'temp_back'  => $this->sellable->tempBookings()->sum("quantity"),
            'temp_front' => $this->sellable->frontBookings()->sum("quantity"),
        ];
    }

    public function getViewModel(): array
    {
        $record = EventSellableServiceStockView::find($this->sellable->id);

        return [
            'bookings'        => (int)$record?->bookings_count,
            'temp_back'       => (int)$record?->temp_bookings_count,
            'temp_front'      => (int)$record?->temp_front_bookings_count,
            'temp'            => $record ? $record?->temp_front_bookings_count + $record->temp_bookings_count : 0,
            'total_bookings'  => (int)$record?->total_bookings_count,
            'available'       => $record?->available,
            'available_label' => $record?->available_label,
        ];
    }


    public function getBookingsCount(): int
    {
        return array_sum($this->getBookingsSubCounts());
    }

    private static $store = [];


    public static function cheapestForEvent(Event $event): int
    {
        $cacheKey = 'events.'.$event->id.'cheapest-sellable';

        // Retrieve the cached nested array
        $cachedPrice = NestedCache::get($cacheKey);

        // Check if the value is already cached
        if ($cachedPrice !== null) {
            return $cachedPrice;
        }

        // Calculate the cheapest sellable service amount
        $today  = Carbon::today();
        $prices = $event
            ->sellableService()
            ->with(['prices'])
            ->get()
            ->flatMap(function ($service) use ($today) {
                return collect($service->prices)->filter(function ($price) use ($today) {
                    $ends = $price['ends'] ? Carbon::createFromFormat('d/m/Y', $price['ends']) : null;

                    return is_null($ends) || $ends->gte($today);
                })->pluck('price');
            });

        $cheapestPrice = (int)$prices->min();

        // Store the calculated value in the cache
        NestedCache::add($cacheKey, $cheapestPrice);

        return $cheapestPrice;
    }


    public static function getRelevantPrice(Sellable $service): int
    {
        $price = $service
            ->prices
            ->filter(fn($item) => ( ! $item->ends || Carbon::createFromFormat('Y-m-d', $item->getRawOriginal('ends'))->gte(now())))
            ->sortBy('ends')
            ->first();

        $total = $price?->price ?: 0;

        if ($service->deposit) {
            $total += Deposits::getSellableDepositAmount($service->deposit);
        }

        return $total;
    }


    public static function getPublishedNonChoosableServices(Event $event): Collection
    {
        return $event
            ->sellableService()
            ->where('published', '=', 1)
            ->whereNull('is_invitation')
            ->get();
    }

    public static function getFrontAvailableServices(Event $event, EventContact $eventContact): Collection
    {
        $services = SellableAccessor::getEventContactPublishedNonChoosableServices($event, $eventContact);

        $frontCart = new FrontCartAccessor();

        return $services->filter(function ($service) use ($frontCart) {
            if (
                ! $service->stock_unlimited
                && ! $service->stock
                && ! $frontCart->hasSellable($service)
            ) {
                return false;
            }

            return true;
        });
    }

    /*
     * TODO
     * RETOURNE FALSE POSITIVE SUR LE METIER
     * */
    public static function getEventContactPublishedNonChoosableServices(Event $event, EventContact $eventContact): Collection
    {
        $professionId    = $eventContact->profile->profession_id;
        $participationId = $eventContact->participation_type_id;

        return $event
            ->sellableService()
            ->with('participations', 'professions', 'group', 'groupCombined', 'place', 'room', 'deposit')
            ->whereNotNull('published')
            ->whereNull('is_invitation')
            ->get()
            ->filter(fn($event) => $event->participations->contains('id', $participationId) && $event->professions->contains('id', $professionId));
    }


    public static function getEventContactPublishedNonChoosableServicesCount(int $event_id, int $user_id): int
    {
        $count = DB::selectOne(
            'SELECT COUNT(DISTINCT ess.id) AS count
            FROM event_sellable_service ess
            LEFT JOIN event_profession epr ON ess.event_id = epr.event_id
            LEFT JOIN event_participation ep ON ess.event_id = ep.event_id
            LEFT JOIN events_contacts ec ON ess.event_id = ec.event_id
            LEFT JOIN account_profile ap ON ap.user_id = ec.user_id
            WHERE ess.event_id = :event_id
              AND ec.user_id = :user_id
              AND ess.published IS NOT NULL
              AND ess.is_invitation IS NULL
              AND ep.participation_id = ec.participation_type_id
              AND epr.profession_id = ap.profession_id',
            [
                'event_id' => $event_id,
                'user_id'  => $user_id,
            ],
        );


        return $count?->count ?? 0;
    }


}
