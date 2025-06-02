<?php

namespace App\Livewire\Front\User;

use App\Accessors\Dates;
use App\Http\Requests\AccountCardRequest;
use App\Livewire\BaseComponent;
use App\Models\Account;
use App\Models\AccountCard;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoyaltyCardSection extends BaseComponent
{


    public Account $account;


    public string $modalTitle;

    //
    public string $id;
    public string $name;
    public string $serial;
    public string $expires_at;

    public function __construct()
    {
        $this->id = 0;
    }

    public function render()
    {
        return view('livewire.front.user.loyalty-card-section');
    }


    public function resetLoyaltyCard()
    {
        $this->reset(['id', 'name', 'serial', 'expires_at']);
    }

    public function save()
    {
        try {
            $dateFormat = Dates::getFrontDateFormat();
            $validation = (new AccountCardRequest($this->account))->removePrefix()->setDateFormat($dateFormat);

            $validatedData = $this->validate($validation->rules(), $validation->messages());

            if ($validatedData['expires_at']) {
                $validatedData['expires_at'] = Carbon::createFromFormat($dateFormat, $validatedData['expires_at'])->format('Y-m-d');
            }


            $model = $this->id ? $this->account->cards()->find($this->id) : new AccountCard();

            if ($this->id) {
                if ($model->user_id !== $this->account->id) {
                    $this->notTheOwner();
                }
            }

            $model->fill($validatedData);
            $this->account->cards()->save($model);

            if (!$this->id) {
                $this->reset(['name', 'serial', 'expires_at']);
            }


            $this->dispatch("loyaltyCardSaved");

        } catch (Throwable $e) {
            $displayMessage = $this->getDisplayMessage($e);
            Log::error($e->getMessage());
            $this->addError('saveException', $displayMessage);
        }

    }

    public function load(int $id)
    {
        $item = $this->account->cards()->find($id);

        if ($item) {
            $f = Dates::getFrontDateFormat();
            $this->id = $item->id;
            $this->name = $item->name;
            $this->serial = $item->serial;
            $this->expires_at = "";
            if ($item->expires_at) {
                $this->expires_at = $item->expires_at->format($f);
            }
        }
    }


    public function delete(AccountCard $item)
    {
        try {
            if ($this->account->id !== $item->user_id) {
                $this->notTheOwner();
            } else {
                $item->delete();
                $this->dispatch("loyaltyCardDeleted");
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
        throw new \Exception("You're not the owner of this loyalty card");
    }
}
