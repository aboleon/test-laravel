<?php

namespace App\Livewire\Front\User;

use App\Accessors\Dates;
use App\Http\Requests\AccountDocumentRequest;
use App\Livewire\BaseComponent;
use App\Models\Account;
use App\Models\AccountDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class IdentityCardSection extends BaseComponent
{


    public Account $account;


    public string $modalTitle;

    //
    public string $id;
    public string $name;
    public string $serial;
    public string $emitted_at;
    public string $expires_at;

    public function __construct(){
        $this->id = 0;
    }

    public function render()
    {
        return view('livewire.front.user.identity-card-section');
    }

    public function resetIdentityCard(){
        $this->reset(['id', 'name', 'serial', 'emitted_at', 'expires_at']);
    }

    public function save()
    {
        try {
            $dateFormat = Dates::getFrontDateFormat();
            $validation = (new AccountDocumentRequest($this->account))->removePrefix()->setDateFormat($dateFormat);

            $validatedData = $this->validate($validation->rules(), $validation->messages());
            $validatedData['emitted_at'] = Carbon::createFromFormat($dateFormat, $validatedData['emitted_at'])->format('Y-m-d');
            $validatedData['expires_at'] = Carbon::createFromFormat($dateFormat, $validatedData['expires_at'])->format('Y-m-d');


            $model = $this->id ? $this->account->documents()->find($this->id) : new AccountDocument();

            if ($this->id) {
                if ($model->user_id !== $this->account->id) {
                    $this->notTheOwner();
                }
            }

            $model->fill($validatedData);
            $this->account->documents()->save($model);

            if (!$this->id) {
                $this->reset(['name', 'serial', 'emitted_at', 'expires_at']);
            }


            $this->dispatch("identityCardSaved");

        } catch (Throwable $e) {
            $displayMessage = $this->getDisplayMessage($e);
            Log::error($e->getMessage());
            $this->addError('saveException', $displayMessage);
        }

    }

    public function load(int $id)
    {
        $document = $this->account->documents()->find($id);

        if ($document) {
            $f = Dates::getFrontDateFormat();
            $this->id = $document->id;
            $this->name = $document->name;
            $this->serial = $document->serial;
            $this->emitted_at = $document->emitted_at->format($f);
            $this->expires_at = $document->expires_at->format($f);
        }
    }


    public function delete(AccountDocument $doc)
    {
        try {
            if ($this->account->id !== $doc->user_id) {
                $this->notTheOwner();
            } else {
                $doc->delete();
                $this->dispatch("identityCardDeleted");
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
        throw new \Exception("You're not the owner of this document");
    }
}
