<?php

namespace App\Http\Controllers\Front\User;

use App\Helpers\Vendor\Propaganistas\LaravelPhone\PhoneNumber;
use App\Http\Controllers\Front\EventBaseController;
use App\Mail\Front\WantToBeMainContactMail;
use App\Models\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendMainContactMailController extends EventBaseController
{

    /**
     * @throws \Exception
     */
    public function sendMainContactMail(string $locale, Event $event)
    {
        // validation
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'address' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'country' => 'required',
            'phone' => ['nullable', 'phone:country_code'],
            'country_code' => ['nullable', 'required_with: phone'],
        ], [
            'name.required' => "Le nom est obligatoire",
            'address.required' => "L'adresse est obligatoire",
            'zip.required' => "Le code postal est obligatoire",
            'city.required' => "La ville est obligatoire",
            'country.required' => "Le pays est obligatoire",
            'phone.phone' => "Le couple indicatif + numéro de téléphone n'est pas valide",
            'country_code.required_with' => "L'indicatif téléphonique est obligatoire lorsque le numéro de téléphone est renseigné",
        ]);
        $data = $validator->validate();

        $ec = $this->getEventContact();
        $userName = $ec->user->names();
        $eventName = $event->texts->name;


        if ($data['phone']) {
            $phone = new PhoneNumber($data['phone'], $data['country_code']);
            $phoneNumber = $phone->formatE164();
        } else {
            $phoneNumber = "";
        }


        $adminUser = $event->adminSubs ?: $event->admin;
        $email = $adminUser->email;

        Mail::to($email)->send(new WantToBeMainContactMail(
            $userName,
            $eventName,
            $data['name'],
            $data['address'],
            $data['zip'],
            $data['city'],
            $data['country'],
            $phoneNumber
        ));

        $ec->fo_group_manager_request_sent = 1;
        $ec->save();

        return redirect()
            ->back()
            ->withInput()
            ->with('success', 'Votre demande a bien été envoyée. Nous vous contacterons prochainement.');
    }


}
