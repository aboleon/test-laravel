<?php

namespace App\Accessors\EventManager;

use App\Enum\EventDepositStatus;
use App\Enum\OrderType;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventDepositStats
{
    private string $depositNet;
    private string $depositVat;
    private string $depositTotal;
    private ?Event $event;

    private array $deposits;

    private ?string $shoppable_type;

    public function __construct(?Event $event = null, ?string $shoppable_type = OrderType::GRANTDEPOSIT->value)
    {
        $this->event = $event;
        $this->shoppable_type = $shoppable_type;
        $this->initializeDepositTotals();
    }

    public function getStatusOrder(): array
    {
        return [
            EventDepositStatus::UNPAID->value   => 'Cautions en attente de paiement',
            EventDepositStatus::PAID->value     => 'Cautions payées',
            EventDepositStatus::BILLED->value   => 'Cautions facturées',
            EventDepositStatus::REFUNDED->value => 'Cautions remboursées',
          //  'to_refund'                         => 'Cautions à rembourser',
            'all'                               => 'TOTAL',
        ];
    }

    private function initializeDepositTotals(): void
    {
        $this->getDepositTotals();

        $this->depositNet   = $this->deposits['all']['total_net'];
        $this->depositVat   = $this->deposits['all']['total_vat'];
        $this->depositTotal = $this->deposits['all']['total'];
    }

    private function getDepositTotals(): array
    {
        $query = DB::table('event_deposits')
            ->where('shoppable_type', $this->shoppable_type)
            ->where('status', '!=', EventDepositStatus::TEMP->value);

        if ($this->event) {
            $query->where('event_id', $this->event->id);
        }

        $totalsForAll = [
            'status'    => 'all',
            'total_net' => 0,
            'total_vat' => 0,
            'total'     => 0,
            'count'     => 0,
        ];

        $results = $query
            ->selectRaw(
                "status,
                 SUM(total_net) as total_net,
                 SUM(total_vat) as total_vat,
                 SUM(total_net + total_vat) as total,
                 COUNT(*) as count",
            )
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $keys = EventDepositStatus::keys();
        array_pop($keys);
        foreach ($keys as $status) {
            $data = $results->get(
                $status,
                (object)[
                    'status'    => $status,
                    'total_net' => 0,
                    'total_vat' => 0,
                    'total'     => 0,
                    'count'     => 0,
                ],
            );

            $totalsForAll['total_net'] += $data->total_net;
            $totalsForAll['total_vat'] += $data->total_vat;
            $totalsForAll['total']     += $data->total;
            $totalsForAll['count']     += $data->count;

            $this->deposits[$data->status] = [
                'status'    => $data->status,
                'total_net' => $data->total_net / 100,
                'total_vat' => $data->total_vat / 100,
                'total'     => $data->total / 100,
                'count'     => $data->count,
            ];
        }
/*
        $this->deposits['to_refund']             = [
            'status'    => 'to_refund',
            'total_net' => $this->deposits[EventDepositStatus::PAID->value]['total_net'] - $this->deposits[EventDepositStatus::REFUNDED->value]['total_net'],
            'total_vat' => $this->deposits[EventDepositStatus::PAID->value]['total_vat'] - $this->deposits[EventDepositStatus::REFUNDED->value]['total_vat'],
            'total'     => $this->deposits[EventDepositStatus::PAID->value]['total'] - $this->deposits[EventDepositStatus::REFUNDED->value]['total'],
        ];
*/
        $this->deposits[$totalsForAll['status']] = [
            'status'    => $totalsForAll['status'],
            'total_net' => $totalsForAll['total_net'] / 100,
            'total_vat' => $totalsForAll['total_vat'] / 100,
            'total'     => $totalsForAll['total'] / 100,
            'count'     => $totalsForAll['count'],
        ];

        return $this->deposits;
    }

    public function getDeposits(): array
    {
        return $this->deposits;
    }

    public function getDepositNet(): float
    {
        return $this->depositNet;
    }

    public function getDepositVat(): float
    {
        return $this->depositVat;
    }

    public function getDepositTotal(): float
    {
        return $this->depositTotal;
    }

}
