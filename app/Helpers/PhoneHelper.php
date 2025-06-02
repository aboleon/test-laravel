<?php

namespace App\Helpers;

use App\Helpers\Vendor\Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\Account;
use App\Models\AccountPhone;

class PhoneHelper
{

    /**
     * @param Account $account
     * @return PhoneNumber|null
     *
     *
     * Note: once you get the $phone from this method, you can do things like this:
     * //dd($phone->formatNational());
     * //dd($phone->getCountry());
     */
    public static function getDefaultPhoneNumberByAccount(Account $account): PhoneNumber|null
    {

        // this is more permissive...
//        $defaultPhone = $account->phones->sortByDesc('default')->first();
//
//
        // ...but i believe strict is better
        $defaultPhone = $account->phones->where('default', 1)->first();
        if ($defaultPhone) {
            try {
                $phone = $defaultPhone->phone;
            } catch (\UnexpectedValueException $e) {
                if ("Queried value for phone is not in international format" === $e->getMessage()) {
                    self::normalizeAccountPhoneNumber($account);
                    $defaultPhone = $account->phones->where('default', 1)->first();
                }
            }
            // note: the country code is guessed automatically as long as the phone is in E.164.
            // However, since the database might contain other formats recorded before,
            // it is better to specify the country code from the database, just to be sure.
            return new PhoneNumber($defaultPhone->phone, $defaultPhone->country_code);
        }
        return null;
    }


    public static function getPhoneNumberByPhoneModel(AccountPhone $accountPhone): PhoneNumber
    {
        return new PhoneNumber($accountPhone->phone, $accountPhone->country_code);
    }


    public static function normalizePhoneNumbersFromAccountPhoneTable(array &$errors = []): void
    {
        $phones = AccountPhone::all();
        foreach ($phones as $phone) {
            $originalPhone = $phone->getRawOriginal('phone');
            try {

                $phone->phone = (new PhoneNumber($originalPhone, $phone->country_code))->formatE164();
                $phone->save();

            } catch (\Exception $e) {
                $errors[$phone->id] = [
                    'phone' => $originalPhone,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }


    public static function normalizeAccountPhoneNumber(Account $account)
    {
        foreach($account->phones as $phone){
            $originalPhone = $phone->getRawOriginal('phone');
            $phone->phone = (new PhoneNumber($originalPhone, $phone->country_code))->formatE164();
            $phone->save();
        }
    }
}