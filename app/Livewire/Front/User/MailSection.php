<?php

namespace App\Livewire\Front\User;

use App\Http\Requests\AccountMailRequest;
use App\Models\Account;
use App\Models\AccountMail;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class MailSection extends Component
{

    public ?Account $account = null;
    public bool $showSubmitButton = true;


    public string $modalEmailTitle;
    //
    public string $id;
    public string $email;

    public string $mainEmail;


    protected $listeners
        = [
            'validateMainEmail' => 'saveMainEmail',
        ];


    public function render()
    {
        return view('livewire.front.user.mail-section');
    }

    public function saveMainEmail()
    {
        try {
            $this->resetErrorBag();

            if ($this->account->email === $this->mainEmail) {
                goto success;
            }


            $account = $this->account;


            $dataToValidate = [
                "email" => $this->mainEmail,
            ];

            $validation = (new AccountMailRequest($account))
                ->removePrefix()
                ->checkDefault();
            $validator  = \Validator::make(
                $dataToValidate,
                $validation->rules(),
                $validation->messages(),
            );

            if ($validator->fails()) {
                $customMessages = $validator->errors()->all();
                foreach ($customMessages as $customMessage) {
                    $this->addError('saveMainEmailValidation', $customMessage);
                }
            } else {
                success:

                $this->account->update(["email" => $this->mainEmail]);
                session()->flash('mail.success', __('Email updated successfully.'));
                session()->flash('mail.warning', "Merci de noter que votre identifiant de connexion est désormais : {$this->mainEmail}");
                $this->dispatch('mainEmailSaved');
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->addError('saveMainEmailException', $e->getMessage());
        }
    }

    public function saveEmail()
    {
        try {
            $account    = $this->account;
            $validation = (new AccountMailRequest($account))
                ->removePrefix()
                ->checkDefault();

            if ($this->id) {
                $validation->setId($this->id);
            }

            $validatedData = $this->validate($validation->rules(), $validation->messages());


            $model = $this->id ? $account->mails()->find($this->id) : new AccountMail();

            if ($this->id) {
                if ($model->user_id !== $account->id) {
                    $this->notTheOwner();
                }
            }

            $model->fill($validatedData);
            $account->mails()->save($model);

            if ( ! $this->id) {
                $this->reset(['email']);
            }


            $this->dispatch("emailSaved");
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->addError('saveEmailException', $e->getMessage());
        }
    }

    public function loadEmail(int $id)
    {
        $item = $this->account->mails()->find($id);
        if ($item) {
            $this->id    = $item->id;
            $this->email = $item->email;
        }
    }

    public function makeEmailDefault(int $id)
    {
        $item = $this->account->mails()->find($id);
        if ($item) {
            try {
                $email                = $item->email;
                $userEmail            = $this->account->email;
                $this->account->email = $email;
                $item->email          = $userEmail;
                $this->account->save();
                $item->save();
            } catch (Throwable $e) {
                Log::error($e->getMessage());
                $this->addError('makeEmailDefault', $e->getMessage());
            }
        }
    }

    public function deleteEmail(AccountMail $item)
    {
        try {
            if ($this->account->id !== $item->user_id) {
                $this->notTheOwner();
            } else {
                $item->delete();
                $this->dispatch("emailDeleted");
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
}
