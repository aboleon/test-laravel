<?php

namespace App\Mailer;

use App\Actions\Front\AutoConnectHelper;
use App\Mail\MailerMail;
use App\Models\EventContact;
use App\Models\EventManager\EventGroup;
use App\Models\User;
use App\Traits\EventCommons;
use Exception;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewGroupMemberAddedNotification extends MailerAbstract
{
    use EventCommons;

    private User $user;
    private EventGroup $eventGroup;


    public string $event;
    public string $groupManager;
    public string $banner;
    public string $account;
    public string $autoConnectUrl;

    /**
     * @throws Exception
     */
    public function send()
    {
        [$userId, $eventGroupId] = explode('-', $this->identifier);
        try {
            $this->user = User::findOrFail($userId);
        } catch (Throwable) {
            throw new Exception("User not found with id ".$userId);
        }
        try {
            $this->eventGroup = EventGroup::find($eventGroupId);
        } catch (Throwable) {
            throw new Exception("EventGroup not found with id ".$this->eventGroup);
        }


        $event = $this->eventGroup->event;

        $ec = EventContact::where('user_id', $this->user->id)
            ->where('event_id', $event->id)
            ->first();

        if ( ! $ec) {
            throw new Exception("EventContact not found with user_id ".$this->user->id." and event_id ".$event->id);
        }

        $this->banner       = $this->getBanner($this->eventGroup->event,'banner_large');
        $this->event        = $event->texts->name;
        $this->groupManager = $this->eventGroup->mainContact->names();
        $this->account      = $this->user->names().' / '.$this->user->email;

        $this->autoConnectUrl = AutoConnectHelper::generateAutoConnectUrlForEventContact($ec);

        return Mail::send(new MailerMail($this));
    }

    public function email(): string|array
    {
        return $this->eventGroup->mainContact->email;
    }


    public function subject(): string
    {
        return __('front/groups.user_attached_to_event', ['event' => $this->event, 'user' => $this->user->names()]);
    }

    public function view(): string
    {
        return 'mails.mailer.new-group-member-added-notification';
    }

}
