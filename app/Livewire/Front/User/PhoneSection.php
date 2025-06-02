<?php

namespace App\Livewire\Front\User;

use App;
use App\Accessors\Accounts;
use App\Http\Requests\AccountPhoneRequest;
use App\Models\Account;
use App\Models\AccountPhone;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class PhoneSection extends Component
{
    public ?Account $account = null;
    public bool $showSubmitButton = true;
    public string $modalPhoneTitle;
    //
    public string $id;
    public string $name;
    public string $phone;
    public bool $default;
    public string $phone_input = '';
    public string $country_code = '';


    protected $listeners = [
        'validateMainPhone' => 'saveMainPhone'
    ];

    public function __construct()
    {
        $this->country_code = '';
    }

    public function render()
    {
        return view('livewire.front.user.phone-section');
    }

    public function savePhone()
    {
        try {
            $account = $this->account;


            $validation = (new AccountPhoneRequest())->removePrefix();

            try {
                $validatedData = $this->validate($validation->rules(), $validation->messages());
            } catch(ValidationException  $v) {
                dd($v);
            }
            $validatedData = $this->validate($validation->rules(), $validation->messages());

            $model = $this->id ? $account->phones()->find($this->id) : new AccountPhone();

            if ($this->id) {
                if ($model->user_id !== $account->id) {
                    $this->notTheOwner();
                }
            }


            $model->fill($validatedData);
            $account->phones()->save($model);

            if (!$this->id) {
                $this->reset(['name', "country_code", 'phone']);
            }


            $this->dispatch("phoneSaved");

        } catch (Throwable $e) {
            //$messages = collect($validator->errors()->all())->join(' ');
            //
            $this->addError('savePhoneException', $e->getMessage());
        }
    }

    public function loadPhone(int $id)
    {
        $item = $this->account->phones()->find($id);
        if ($item) {
            $phoneNumber = App\Helpers\PhoneHelper::getPhoneNumberByPhoneModel($item);
            $this->id = $item->id;
            $this->name = (string)$item->name;
            $this->country_code = $item->country_code;
            $this->phone = $phoneNumber->formatE164();
            $this->phone_input = $phoneNumber->formatNational();

            // JS will set #phone_input field with formatNational manually
            $this->dispatch('setPhoneInput', $phoneNumber->formatNational(), $this->country_code);
        }
    }

    public function makePhoneDefault(int $id)
    {

        $account = $this->account;
        $item = $account->phones()->find($id);

        if ($item) {
            $defaultPhone = Accounts::getDefaultPhoneModelByAccount($account);
            if ($defaultPhone && $defaultPhone->id !== $item->id) {
                $defaultPhone->default = null;
                $defaultPhone->save();
            }
            $item->default = 1;
            $item->save();

            $this->account = $this->account->fresh();
        }
    }

    public function deletePhone(AccountPhone $item)
    {
        try {
            if ($this->account->id !== $item->user_id) {
                $this->notTheOwner();
            } else {
                $item->delete();
                $this->dispatch("phoneDeleted");
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->dispatch('deleteError', $e->getMessage());
        }
    }

//--------------------------------------------
//
//--------------------------------------------
    private function notTheOwner()
    {
        throw new \Exception("You're not the owner of this email address");
    }
    public function resetForm()
    {
        $this->reset([
            'name',
            'phone_input',
            'phone',
            'country_code',
            'default'
        ]);

        // Reset the international telephone input if it exists
        $this->dispatch('resetPhoneInput');
    }
}
