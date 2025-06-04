<?php

namespace App\Accessors;

use App\DataTables\View\EventSellableServiceStockView;
use App\Models\Event;
use App\Models\EventTexts;
use App\Traits\EventCommons;
use App\Traits\Models\EventModelTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MetaFramework\Accessors\Countries;
use MetaFramework\Mediaclass\Config as MediaclassConfig;
use Throwable;

class EventAccessor
{
    use EventCommons;
    use EventModelTrait;

    public function __construct(?Event $event = null)
    {
        $this->setEvent($event);
    }

    public function contacts(): ?Collection
    {
        return Accounts::getContactsFromPool($this->event->contacts->pluck('user_id')->toArray());
    }

    public function clients(): ?Collection
    {
        return Accounts::getContactsFromPool($this->event->clients->pluck('user_id')->toArray());
    }

    public static function eventsArray(): array
    {
        return EventTexts::select('events_texts.name', 'events_texts.event_id')
            ->join('events', 'events_texts.event_id', '=', 'events.id')
            ->whereNull('events.deleted_at')
            ->get()
            ->pluck('name', 'event_id')
            ->sort()
            ->toArray();
    }

    // Get the countries of the registered accounts for the event
    public function representedCountries(): array
    {
        $countries = Countries::orderedCodeNameArray();

        return collect(
            DB::table('events_contacts')
                ->join('account_address', 'events_contacts.user_id', '=', 'account_address.user_id')
                ->where('events_contacts.event_id', $this->event->id)
                ->distinct()
                ->pluck('account_address.country_code'),
        )
            ->filter()
            ->mapWithKeys(fn($code) => [$code => $countries[$code] ?? $code])
            ->toArray();
    }

    // Get the countries of the establishments for the registered accounts for the event
    public function representedEstablishmentCountries(): array
    {
        $countries      = Countries::orderedCodeNameArray();
        $establishments = $this->representedEstablishments();

        return collect(
            DB::table('establishments')
                ->whereIn('id', array_keys($establishments))
                ->distinct()
                ->pluck('country_code'),
        )
            ->filter()
            ->mapWithKeys(fn($code) => [$code => $countries[$code] ?? $code])
            ->toArray();
    }

    public function representedParticipationTypes(): array
    {
        $types = Dictionnaries::participationTypesListable();

        return collect(
            DB::table('events_contacts')
                ->where('events_contacts.event_id', $this->event->id)
                ->distinct()
                ->pluck('participation_type_id'),
        )
            ->filter()
            ->mapWithKeys(fn($code) => [$code => $types[$code] ?? $code])
            ->toArray();
    }

    public function representedEstablishments(): array
    {
        $data = Establishments::orderedIdNameArray();

        return collect(
            DB::table('events_contacts')
                ->join('account_profile', 'events_contacts.user_id', '=', 'account_profile.user_id')
                ->where('events_contacts.event_id', $this->event->id)
                ->distinct()
                ->pluck('account_profile.establishment_id'),
        )
            ->filter()
            ->mapWithKeys(fn($code) => [$code => $data[$code] ?? $code])
            ->toArray();
    }


    public static function getDashboardEvents(?string $keywords = ''): array
    {
        $keywords = trim(Str::lower($keywords));

        $now          = now()->format('Y-m-d');
        $twoMonthsAgo = now()->subMonths(2)->format('Y-m-d');

        $query          = self::getDashboardEventBuilder($keywords);
        $upcomingEvents = (clone $query)
            ->where('starts', '>=', $now)
            ->orderBy('starts')
            ->get()
            ->map(function ($event) {
                $media                 = $event->media->first();
                $event->media_url      = $media?->url();
                $event->media_is_image = str_starts_with($media?->mime, 'image/');

                return $event;
            });

        $pastEvents = (clone $query)
            ->whereBetween('starts', [$twoMonthsAgo, $now])
            ->orderBy('starts', 'desc')
            ->get()
            ->map(function ($event) {
                $media                 = $event->media->first();
                $event->media_url      = $media?->url();
                $event->media_is_image = str_starts_with($media?->mime, 'image/');

                return $event;
            });

        return [
            'upcomingEvents' => $upcomingEvents,
            'pastEvents'     => $pastEvents,
        ];
    }


    public static function getPassedEvents(?string $expression = null): Collection
    {
        $b = self::getDashboardEventBuilder($expression)->where('ends', '<', now()->format('Y-m-d'));
        if ($expression) {
            $b->where(fn($q)
                => $q
                ->whereRaw('events.id LIKE ?', ['%'.$expression.'%'])
                ->orWhereRaw("LOWER(json_unquote(json_extract(events_texts.name, '$.fr'))) LIKE ?", ['%'.$expression.'%']),
            );
        }

        return $b->get()->sortByDesc('ends');
    }


    public static function getBannerUrlByEvent(Event $event, string $group = 'banner_large'): string|null
    {
       $banner = new self($event)->getBanner($event, $group);
       if ($banner) {
           return asset($banner);
       }
       return null;
    }

    public static function getEventFrontUrl(Event $event, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return route('front.event.show', [
            'locale' => $locale,
            'event'  => $event->id,
            'slug'   => Str::slug($event->texts?->name) ?: 'undefined',
        ]);
    }
    // TODO: get rid of static method
    public function getFrontUrl(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return route('front.event.show', [
            'locale' => $locale,
            'event'  => $this->event->id,
            'slug'   => Str::slug($this->event->texts?->name) ?: 'undefined',
        ]);
    }

    public function generateTimeRange(int $delta = 2): CarbonPeriod
    {
        return new CarbonPeriod(
            Carbon::createFromFormat('d/m/Y', $this->event->starts)->subDays($delta),
            Carbon::createFromFormat('d/m/Y', $this->event->ends)->addDays($delta),
        );
    }

    public static function getAdminSubscriptionEmail(Event $event): string
    {
        return $event->adminSubs?->email ?: config('app.default_mail');
    }

    public function adminEmail(): string
    {
        return $this->event->adminSubs?->email ?: ($this->event->admin?->email ?: config('app.default_mail'));
    }

    public function adminName(): string
    {
        try {
            return $this->event->adminSubs ? $this->event->adminSubs->names() : $this->event->admin->names();
        } catch (Throwable) {
            return __('front/ui.the_admin');
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getDashboardEventBuilder(?string $expression = null): Builder
    {
        $query = Event::query()
            ->select(
                'events.id',
                'events.starts',
                'events.ends',
                'events_texts.name->fr as name',
                'events_texts.subname->fr as subname',
                'family.name->fr as family_name',
                'type.name->fr as type_name',
                'users_admin.first_name as admin',
                'users_subadmin.first_name as subadmin',
                'users_pecadmin.first_name as pecadmin',
                'users_grantadmin.first_name as grantadmin',
            )
            ->join('events_texts', 'events.id', '=', 'events_texts.event_id')
            ->join('events_pec', 'events.id', '=', 'events_pec.event_id')
            ->join('users as users_admin', 'events.admin_id', '=', 'users_admin.id')
            ->leftJoin('users as users_subadmin', 'events.admin_subs_id', '=', 'users_subadmin.id')
            ->leftJoin('users as users_pecadmin', 'events_pec.admin_id', '=', 'users_pecadmin.id')
            ->leftJoin('users as users_grantadmin', 'events_pec.grant_admin_id', '=', 'users_grantadmin.id')
            ->leftJoin('dictionnary_entries as family', 'events.event_main_id', '=', 'family.id')
            ->leftJoin('dictionnary_entries as type', 'events.event_type_id', '=', 'type.id')
            ->with(['media']);

        if ($expression) {
            $query->where(fn($q)
                => $q
                ->whereRaw('events.id LIKE ?', ['%'.$expression.'%'])
                ->orWhereRaw("LOWER(json_unquote(json_extract(events_texts.name, '$.fr'))) LIKE ?", ['%'.$expression.'%']),
            );
        }

        return $query;
    }

    public function toEventStart(): int
    {
        return Carbon::now()->diffInDays(Carbon::createFromFormat('d/m/Y', $this->event->starts), false);
    }

    public function sinceEventEnd(): int
    {
        return Carbon::createFromFormat('d/m/Y', $this->event->ends)->diffInDays(now(), false);
    }

    public function eventDuration(): int
    {
        return Carbon::createFromFormat('d/m/Y', $this->event->starts)->diffInDays(Carbon::createFromFormat('d/m/Y', $this->event->ends));
    }

    public function timeline(): array
    {
        $now     = now();
        $starts  = Carbon::createFromFormat('d/m/Y', $this->event->starts);
        $passed  = $now->gt($starts);
        $ongoing = $now->between($starts, Carbon::createFromFormat('d/m/Y', $this->event->ends));

        return [
            'coming'        => $this->hasNotStarted(),
            'passed'        => $passed,
            'ongoing'       => $ongoing,
            'passed_since'  => $this->sinceEventEnd(),
            'days_to_event' => $this->toEventStart(),
            'state'         => $passed ? 'passed' : ($ongoing ? 'ongoing' : 'coming'),
        ];
    }

    public function hasNotStarted(): bool
    {
        try {
            return now()->lt(Carbon::createFromFormat('d/m/Y', $this->event->starts));
        } catch (Throwable) {
            return false;
        }
    }

    public function availableSellablesStocks(): array
    {
        return EventSellableServiceStockView::where('event_id', $this->event->id)->pluck('available', 'id')->toArray();
    }

    public function availableSellableStockFor(int $sellable_id): int
    {
        return EventSellableServiceStockView::where('id', $sellable_id)->value('available');
    }

    public function eventName(): string
    {
        return rtrim($this->event->texts->name.' / '.$this->event->texts->subname, '/ ');
    }


}
