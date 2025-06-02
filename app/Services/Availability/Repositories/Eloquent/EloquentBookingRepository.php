<?php

namespace App\Services\Availability\Repositories\Eloquent;

use App\Models\EventManager\Accommodation as Hotel;
use App\Models\Order\Cart\AccommodationCart;
use App\Models\Order\StockTemp;
use App\Services\Availability\Interfaces\BookingRepository;
use App\Services\Availability\ValueObjects\{DateRange};
use App\Services\Availability\ValueObjects\BookingData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EloquentBookingRepository implements BookingRepository
{
    public function getForHotel(Hotel $hotel, DateRange $dateRange): Collection
    {
        $query = AccommodationCart::query()
            ->where('event_hotel_id', $hotel->id)
            ->select(
                'order_id',
                'on_quota',
                'date',
                'room_group_id',
                'quantity',
                'participation_type_id',
                'c.client_id as account_id',
                'c.client_type as account_type',
                'c.amended_by_order_id as was_amended',
                'c.cancelled_at as order_cancelled_at',
                'order_cart_accommodation.cancelled_at as cart_cancelled_at'
            )
            ->join('orders as c', 'c.id', '=', 'order_cart_accommodation.order_id');


        $this->applyDateRangeFilter($query, $dateRange);

        return $query->get()->map(function($booking) {

            $bookingData = new BookingData();
            $bookingData->__set('date', $booking->getRawOriginal('date'));
            $bookingData->__set('room_group_id', $booking->room_group_id);

            return $bookingData
                ->setQuantity($booking->quantity)
                ->setOnQuota($booking->on_quota)
                ->setParticipationType($booking->participation_type_id)
                ->setGroupId($booking->account_type === 'group' ? $booking->account_id : null)
                ->setTemp(false)
                ->setCancelled(!is_null($booking->order_cancelled_at) || !is_null($booking->cart_cancelled_at))
                ->setAmended(!is_null($booking->was_amended));
        });
    }

    public function getTemporaryForHotel(Hotel $hotel, DateRange $dateRange): Collection
    {
        $query = StockTemp::query()
            ->whereIn('shoppable_id', $hotel->roomGroups->pluck('id'));

        $this->applyDateRangeFilter($query, $dateRange);

        return $query->get()->map(function($booking) {
            $bookingData = new BookingData();
            $bookingData->__set('date', $booking->date);
            $bookingData->__set('room_group_id', $booking->shoppable_id);

            return $bookingData
                ->setQuantity($booking->quantity)
                ->setOnQuota($booking->on_quota ?? false)
                ->setParticipationType($booking->participation_type_id)
                ->setGroupId($booking->account_type === 'group' ? $booking->account_id : null)
                ->setTemp(true)
                ->setCancelled(false)
                ->setAmended(false);
        });
    }


    public function getForGroup(Hotel $hotel, int $groupId, DateRange $dateRange): Collection
    {
        return $this->getForHotel($hotel, $dateRange)
            ->filter(fn(BookingData $booking) => $booking->getGroupId() === $groupId);
    }

    private function applyDateRangeFilter(Builder $query, DateRange $dateRange): void
    {
        if ($dateRange->isSingleDate()) {
            $query->where('date', $dateRange->getSingleDate());
            return;
        }

        if ($dateRange->getStartDate()) {
            $query->where('date', '>=', $dateRange->getStartDate());
        }

        if ($dateRange->getEndDate()) {
            $query->where('date', '<', $dateRange->getEndDate());
        }
    }
}
