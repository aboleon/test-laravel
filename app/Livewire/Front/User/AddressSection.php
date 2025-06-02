<?php

namespace App\Livewire\Front\User;

use App\Http\Requests\AccountAddressRequest;
use App\Models\Account;
use App\Models\AccountAddress;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

class AddressSection extends Component
{
    public Account $account;
    public string $modalTitle;

    public string $id = '';
    public ?string $name = null;
    public ?string $company = null;
    public bool $billing = false; // Changed from ?int to bool
    public ?string $text_address = null;
    public ?string $complementary = null;
    public ?string $street_number = null;
    public ?string $route = null;
    public ?string $locality = null;
    public ?string $country_code = null;
    public ?string $postal_code = null;
    public ?string $cedex = null;
    public ?string $lat = null;
    public ?string $lon = null;

    protected $listeners = [
        'AddressSection.checkAtLeastOneAddress' => 'checkAtLeastOneAddress',
    ];

    public function render(): Renderable
    {
        return view('livewire.front.user.address-section');
    }

    public function checkAtLeastOneAddress(): void
    {
        if ($this->account->address->count() > 0) {
            $this->dispatch("AddressSection.atLeastOneAddress");
        }
    }

    public function resetAddress(): void
    {
        $this->id = "";
        $this->name = "";
        $this->company = "";
        $this->billing = false; // Changed from empty string to false
        $this->text_address = "";
        $this->complementary = "";
        $this->street_number = "";
        $this->route = "";
        $this->locality = "";
        $this->country_code = "";
        $this->postal_code = "";
        $this->lat = "";
        $this->lon = "";
        $this->cedex = "";
    }

    public function save(): void
    {
        try {
            if (!$this->text_address) {
                $this->addError("saveException", __('front/account.validation.text_address'));
                return;
            }

            $validation = (new AccountAddressRequest())->rebuildWithNoPrefix();
            $validatedData = $this->validate($validation->rules(), $validation->messages());

            $model = $this->id ? $this->account->address()->find($this->id) : new AccountAddress();

            if ($this->id) {
                if ($model->user_id !== $this->account->id) {
                    $this->notTheOwner();
                }
            }

            // Convert billing bool to int for database
            $validatedData['billing'] = $this->billing ? 1 : 0;

            $model->fill($validatedData);
            $this->account->address()->save($model);

            if (!$this->id) {
                $this->reset([
                    "name",
                    "company",
                    "billing",
                    "text_address",
                    "street_number",
                    "route",
                    "locality",
                    "country_code",
                    "postal_code",
                    "lat",
                    "lon",
                    "complementary",
                    "cedex",
                ]);
            }

            $this->dispatch("addressSaved");
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->addError('saveException', $e->getMessage());
        }
    }

    public function load(int $id): void
    {
        $item = $this->account->address()->find($id);

        if ($item) {
            $this->id = $item->id;
            $this->name = $item->name;
            $this->company = $item->company;
            $this->billing = (bool)$item->billing; // Convert to boolean
            $this->text_address = $item->text_address;
            $this->street_number = $item->street_number;
            $this->route = $item->route;
            $this->locality = $item->locality;
            $this->country_code = $item->country_code;
            $this->postal_code = $item->postal_code;
            $this->cedex = $item->cedex;
            $this->lat = $item->lat;
            $this->lon = $item->lon;
            $this->complementary = (string)$item->complementary;
        }
    }

    public function delete(AccountAddress $item): void
    {
        try {
            if ($this->account->id !== $item->user_id) {
                $this->notTheOwner();
            } else {
                $item->delete();
                $this->dispatch("addressDeleted");
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->dispatch('deleteError', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function notTheOwner(): void
    {
        throw new Exception("You're not the owner of this loyalty card");
    }
}
