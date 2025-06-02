<?php

namespace App\Helpers\Front\Cart;

use MetaFramework\Accessors\Prices;
use MetaFramework\Accessors\VatAccessor;

class CartTotalsUtil
{
    // Store all amount-related properties in an array
    private array $amounts = [
        'serviceTotalTtcWithPec' => 0.0,
        'serviceTotalTtcWithoutPec' => 0.0,
        'stayTotalTtcWithPec' => 0.0,
        'stayTotalTtcWithoutPec' => 0.0,
        'totalTtcWithPec' => 0.0,
        'totalTtcWithoutPec' => 0.0,
        'totalTtcGrantWaiverFees' => 0.0,
        'amendableAmount' => 0.0,
        'amendableAmountVat' => 0.0,
        'amendableAmountNet' => 0.0,
        'detailsTotalNet' => 0.0,
        'detailsVatAmount' => 0.0,
        'nonTaxableTotal' => 0.0,
    ];

    // Keep non-amount properties as individual class members
    public bool $hasServices = false;
    public bool $hasStays = false;
    public bool $hasGrantDeposit = false;
    public bool $showDetails = true;

    public function process(bool $isEligible, array $cartLines = [])
    {
        $serviceLines = $cartLines['services'] ?? null;
        $stayLines = $cartLines['stays'] ?? null;
        $grantWaiverFeesLines = $cartLines['grantWaiverFees'] ?? null;

     //   de($serviceLines);
        //--------------------------------------------
        // services
        //--------------------------------------------
        if ($serviceLines && $serviceLines->isNotEmpty()) {
            $this->hasServices = true;

            $serviceLines->each(function ($line) use ($isEligible) {
                $depositTtc = $line->meta_info['deposit_ttc'] ?? 0;
                if ($depositTtc) {
                    $this->amounts['nonTaxableTotal'] += $depositTtc;
                }

                $this->amounts['serviceTotalTtcWithoutPec'] += $line->total_ttc;
                $this->amounts['serviceTotalTtcWithPec'] += $line->total_ttc - $line->total_pec;

                if (!$line->total_pec) {
                    $this->amounts['detailsTotalNet'] += $line->total_net;
                    $this->amounts['detailsVatAmount'] += $line->total_ttc - $line->total_net;
                    if ($depositTtc) {
                        $this->amounts['detailsVatAmount'] -= $depositTtc;
                    }
                }
            });
        }

        //--------------------------------------------
        // stays
        //--------------------------------------------
        if ($stayLines && $stayLines->isNotEmpty()) {
            $this->hasStays = true;

            $stayLines->each(function ($line) use ($isEligible) {

                $amendable = $line->meta_info['amendable_amount'];

                if ($line->meta_info['amendable_amount']) {
                    $line->total_ttc = $line->total_ttc - $line->meta_info['amendable_amount'] - $line->meta_info['processing_fee_ttc'] ;
                    $line->total_net = $line->total_net - VatAccessor::netPriceFromVatPrice($line->meta_info['amendable_amount'], $line->vat_id) -
                    VatAccessor::netPriceFromVatPrice($line->meta_info['processing_fee_ttc'], $line->meta_info['processing_fee_vat_id']);
                }

                $this->amounts['stayTotalTtcWithoutPec'] += $line->total_ttc;
                $this->amounts['stayTotalTtcWithPec'] += $line->total_ttc - $line->total_pec;
                $this->amounts['detailsTotalNet'] += $line->total_net;
                $this->amounts['detailsVatAmount'] += ($line->total_ttc - $line->total_pec) - $line->total_net;
                $this->amounts['amendableAmount'] += $amendable;
                $this->amounts['amendableAmountNet'] += $amendable ? VatAccessor::netPriceFromVatPrice($amendable, $line->meta_info['vat_id']) : 0;
                $this->amounts['amendableAmountVat'] += $amendable ? VatAccessor::vatForPrice($amendable, $line->meta_info['vat_id']) : 0;

            });
        }

        //--------------------------------------------
        // grant deposit
        //--------------------------------------------
        if ($grantWaiverFeesLines && $grantWaiverFeesLines->isNotEmpty()) {
            $this->hasGrantDeposit = true;
            $this->amounts['totalTtcGrantWaiverFees'] = $grantWaiverFeesLines->first()->total_ttc;
        }

        //--------------------------------------------
        // totals
        //--------------------------------------------
        $this->showDetails = $this->amounts['detailsTotalNet'] > 0;

        $this->amounts['totalTtcWithPec'] =
            $this->amounts['serviceTotalTtcWithPec'] +
            $this->amounts['stayTotalTtcWithPec'] +
            $this->amounts['totalTtcGrantWaiverFees'];

        $this->amounts['totalTtcWithoutPec'] =
            $this->amounts['serviceTotalTtcWithoutPec'] +
            $this->amounts['stayTotalTtcWithoutPec'] +
            $this->amounts['totalTtcGrantWaiverFees'];
    }

    // Optionally, you can create getter methods for accessing amounts
    public function getAmount(string $key): int|float
    {
        return $this->amounts[$key] ?? 0.0;
    }

    public function showAmount(string $key): string
    {
        return Prices::readableFormat(price:$this->getAmount($key));
    }

    public function setAmount(string $key, float $value): void
    {
        if (array_key_exists($key, $this->amounts)) {
            $this->amounts[$key] = $value;
        }
    }

    public function getAmounts(): array
    {
        return $this->amounts;
    }
}
