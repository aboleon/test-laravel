<?php

namespace App\Printers\Event;

use App\DataTables\View\EventDepositView;
use App\Enum\EventDepositStatus;

class Deposit
{
    public static function printStatus(EventDepositView $eventDepositView): string
    {
        $color = match ($eventDepositView->status) {
            EventDepositStatus::PAID->value => 'success',
            EventDepositStatus::BILLED->value => 'warning',
            EventDepositStatus::REFUNDED->value => 'primary',
            EventDepositStatus::UNPAID->value => 'danger',
        };
        return '<span class="badge rounded-pill text-bg-' . $color . '">' . EventDepositStatus::translated($eventDepositView->status) . '</span>';
    }

    public static function printTotalTtc(EventDepositView $eventDepositView): string
    {
        return round($eventDepositView->total_ttc / 100, 2) . ' €';
    }

    public static function printTotalNet(EventDepositView $eventDepositView): string
    {
        if ($eventDepositView->has_invoice) {
            return round($eventDepositView->total_net / 100, 2) . ' €';
        }
        return "";
    }
}
