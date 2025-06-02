<?php

namespace App\Livewire\Front\User;

use App;
use App\Models\Account;
use App\Models\Event;
use App\Models\EventContact;
use Illuminate\Contracts\Support\Renderable;
use Livewire\Component;

class InfoSection extends Component
{

    public ?Account $account = null;
    public Event $event;
    public EventContact $eventContact;
    public ?string $registrationType = null;
    public ?string $context = 'register';
    public bool $showParticipationType = true;
    public array $photo = [];
    public array $domains = [];
    public string $country_code;


    public function __construct()
    {
        $country_code = strtoupper(App::getLocale());
        if ($country_code == 'EN') {
            $country_code = 'GB';
        }
        $this->country_code = $country_code;

    }


    public function render(): Renderable
    {
        return view('livewire.front.user.info-section');
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    private function notTheOwner()
    {
        throw new \Exception("You're not the owner of this loyalty card");
    }
}
