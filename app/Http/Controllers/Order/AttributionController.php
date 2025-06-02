<?php

namespace App\Http\Controllers\Order;

use App\Accessors\GroupAccessor;
use App\Accessors\OrderAccessor;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Group;
use App\Models\Order;
use App\Traits\AttributionTrait;
use Illuminate\Contracts\Support\Renderable;
use Throwable;

class AttributionController extends Controller
{

    use AttributionTrait;

    private readonly Order $order;

    public function index(Event $event, Order $order, string $type): Renderable
    {
        $this->order    = $order;
        $this->event    = $event;
        $this->locale   = app()->getLocale();
        $this->type     = $type;
        $this->forOrder = true;

        try {
            $group               = Group::findOrFail($order->client_id);
            $this->groupAccessor = (new GroupAccessor($group));
        } catch (Throwable) {
            abort(404, "Le groupe pour cette commande ne peut pas être identifié.");
        }

        $this->orderAccessor = (new OrderAccessor($this->order));
        $error               = '';

        if ($this->orderAccessor->isFrontGroupOrder()) {
            $error = "Cette commande a été faite par un group mananager. La gestion des attributions n'est pas autorisée.";
        }

        $this->setGroupMembers();

        $viewData = array_merge(
            $this->baseViewData(),
            [
                'orderAccessor' => $this->orderAccessor,
                'error' => $error,
                'order' => $order,
                'group' => $group,
            ],
        );

        if (method_exists($this, $type.'Data')) {
            $viewData = array_merge($viewData, $this->{$type.'Data'}());
        }

        return view('orders.attributions.index')->with($viewData);
    }

    private function accommodationData(): array
    {
        $this->ordered = collect($this->orderAccessor->accommodationAttributions())->groupBy(['event_hotel_id', 'date']);

        return array_merge(
            [
                'ordered' => $this->ordered,
            ],
            $this->getAccommodationViewData(),
        );
    }


}
