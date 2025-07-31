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
            $newAccount->last_name = $newAccount->last_name . ' - COPIE';
            $newAccount->push();

            // Duplicate HasOne relations
            if ($this->account->profile) {
                $newProfile = $this->account->profile->replicate();
                $newProfile->user_id = $newAccount->id;
                $newProfile->push();
            }

            // Duplicate HasMany relations
            $hasManyRelations = ['address', 'phones', 'documents', 'cards']; // Removed 'mails' from here
            foreach ($hasManyRelations as $relation) {
                // Skip if the relation is activity logs
                if ($relation === 'activities' || $relation === 'activity_log') {
                    continue;
                }

                foreach ($this->account->$relation as $item) {
                    $newItem = $item->replicate();
                    $newItem->user_id = $newAccount->id;
                    $newItem->push();
                }
            }

            // Handle mails relation separately due to unique constraint
            foreach ($this->account->mails as $mail) {
                $newMail = $mail->replicate();
                $newMail->user_id = $newAccount->id;
                // Generate a unique email address for the duplicated mail
                $newMail->email = Str::lower(Str::random(16) . '@' . Str::random(8) . '.com');
                $newMail->push();
            }

            // Duplicate BelongsToMany relations
            $belongsToManyRelations = ['groups']; // Removed 'events' from here
            foreach ($belongsToManyRelations as $relation) {
                $newAccount->$relation()->sync($this->account->$relation->pluck('id')->toArray());
            }

            // Note: We intentionally do NOT duplicate:
            // - events relation (EventGroup associations/history)
            // - activity logs
            // - any attribution history (accommodationAttributions, serviceAttributions)
            // - blocked room history
            // These are historical records that should remain with the original account

            return $newAccount;
        });
    }
}
