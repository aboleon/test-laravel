<?php

namespace App\Traits\Models;

use App\Models\Account;
use MetaFramework\Services\Validation\ValidationModelPropertiesTrait;
use MetaFramework\Traits\Responses;

trait AccountModelTrait
{
    use Responses;
    use ValidationModelPropertiesTrait;

    protected ?Account $account = null;

    public function setAccount(null|int|Account $account): self
    {
        $this->account = is_int($account) ? Account::find($account) : $account;
        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

}
