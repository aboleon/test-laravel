<?php

namespace App\Actions\Account;

use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Replicator
{
    public function __construct(public Account $account)
    {

    }

    public function __invoke(): Account
    {
        return $this->duplicateAccount();
    }

    public function duplicateAccount(): Account
    {
        return DB::transaction(function () {
            // Duplicate the main model
            $newAccount = $this->account->replicate();
            $newAccount->email = Str::lower(Str::random(16) . '@' . Str::random(8) . '.com');
            $newAccount->push();

            // Duplicate HasOne relations
            if ($this->account->profile) {
                $newProfile = $this->account->profile->replicate();
                $newProfile->user_id = $newAccount->id;
                $newProfile->push();
            }

            // Duplicate HasMany relations
            $hasManyRelations = ['address', 'phones', 'documents', 'mails', 'cards'];
            foreach ($hasManyRelations as $relation) {
                foreach ($this->account->$relation as $item) {
                    $newItem = $item->replicate();
                    $newItem->user_id = $newAccount->id;
                    $newItem->push();
                }
            }

            // Duplicate BelongsToMany relations
            $belongsToManyRelations = ['groups', 'events'];
            foreach ($belongsToManyRelations as $relation) {
                $newAccount->$relation()->sync($this->account->$relation->pluck('id')->toArray());
            }

            return $newAccount;
        });
    }
}
