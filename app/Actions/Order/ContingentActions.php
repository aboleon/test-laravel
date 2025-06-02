<?php

namespace App\Actions\Order;

use App\Accessors\EventContactAccessor;
use App\Accessors\EventManager\Availability;
use App\Accessors\EventManager\Availability\AvailabilityRecap;
use App\Enum\OrderClientType;
use App\Models\EventManager\Accommodation;
use App\Models\Order\StockTemp;
use App\Traits\TempStockable;
use MetaFramework\Traits\Ajax;
use Throwable;

class ContingentActions
{
    use Ajax;
    use TempStockable;


    private Availability $availability;
    private array $roomGroup;
    private string $accountType;

    private bool $isGroup = false;
    private array $thisGroup = [];


    public function __construct()
    {
        $this->enableAjaxMode();
        $this->fetchInput();
        $this->fetchCallback();

        $this->setAccountType((string)request('account_type'));


        $this->validateStockableInput();

        $this->prevalue = (int)request('prevalue');
    }

    public function setAccountType(string $accountType): self
    {
        $this->accountType = in_array($accountType, OrderClientType::values()) ? $accountType : OrderClientType::default();

        return $this;
    }

    public function checkAvailability(): void
    {
        $accommodation = Accommodation::query()->find((int)request('event_accommodation_id'));
        $accountType   = (string)request('account_type');
        $this->isGroup = $accountType == OrderClientType::GROUP->value;

        $requestedDate        = (string)request('date');
        $requestedRoomGroupId = (int)request('shoppable_id');

        $this->availability = (new Availability())
            ->setEventAccommodation($accommodation)
            ->setDate($requestedDate)
            ->setRoomGroupId($requestedRoomGroupId)
            ->setParticipationType((int)request('participation_type'));

        if ($this->isGroup) {
            $this->availability->setEventGroupId((int)request('event_group_id'));

            $bookings        = (new AvailabilityRecap($this->availability));
            $bookings_data   = $bookings->get($requestedDate, $requestedRoomGroupId);
            $this->thisGroup = $bookings_data['this_group'];
            $this->responseElement('this_group', $this->thisGroup);
        } else {
            $eventContact = EventContactAccessor::getEventContactByEventAndUser($accommodation->event, (int)request('account_id'));
            $this->availability->setEventContact($eventContact);
        }

        $this->roomGroup = $this->availability->getRoomGroup();
    }

    public function decreaseStock(): array
    {
        $this->checkAvailability();

        $controlQty = $this->quantity;


        if ($this->quantity >= $this->prevalue) {
            $controlQty = $this->quantity - $this->prevalue;
        }

        $availability = $this->remainingAvailability($this->availability->getRoomGroupAvailability());

        $this->responseElement('controlQty', $controlQty);
        $this->responseElement('block_booking', $availability < $controlQty);


        if ($availability < $controlQty) {
            $this->responseError("Il ne reste plus de la disponibilité ".$this->roomGroup['name'].' pour le '.$this->availability->get('dates')['formatted']['date']);
        }

        if ( ! $this->hasErrors()) {
            $this->prevalue = (int)request('prevalue');

            $this->processStockTempObject();
        }

        if ($this->hasErrors()) {
            return $this->fetchResponse();
        }

        try {
            $this->checkAvailability();

            $this->responseElement('evaluated_stock_qty', $controlQty);
            $this->responseElement('isGroup', $this->isGroup);

            $this->remainingAvailability($this->availability->getRoomGroupAvailability());

            $this->successMessage();
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    protected function remainingAvailability(int $availability): int
    {
        $remaining = $availability;

        $this->responseElement('before_stock', $remaining);

        if ($this->isGroup && $this->thisGroup && $this->thisGroup['blocked'] > 0) {

            if ($this->thisGroup['remaining'] == 0 && $this->thisGroup['booked']['on_quota'] < $this->thisGroup['blocked']) {
                $remaining = 0;
                $this->responseElement('before_stock', 0);
                $this->responseElement('block_booking', true);
            }
        }

        $this->responseElement('updated_stock', $remaining);
        return $remaining;
    }

    public function increaseStock(): array
    {
        $this->prevalue = (int)request('prevalue');

        $this->processStockTempObject();

        try {
            $this->checkAvailability();

            $this->responseElement('updated_stock', $this->availability->getRoomGroupAvailability());
            $this->successMessage();
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    public function clearTempStock(): array
    {
        try {
            StockTemp::where($this->setStockTempData())->delete();

            $this->checkAvailability();

            $this->responseElement('updated_stock', $this->availability->getRoomGroupAvailability());
            $this->responseElement('before_stock', request('before_stock'));
            $this->responseElement('shoppable_id', (int)request('shoppable_id'));
            $this->responseSuccess("La disponibilité a été remise.");
        } catch (Throwable $e) {
            $this->responseException($e);
        }

        return $this->fetchResponse();
    }

    private function successMessage(): void
    {
        $this->responseSuccess("La disponibilité pour ".$this->roomGroup['name'].' pour le '.$this->availability->get('dates')['formatted']['date']." a été mise à jour");
    }


}
