<?php
namespace App\MailTemplates\Traits\Variables;

use App\Actions\Front\AutoConnectHelper;
use App\Enum\OrderClientType;
use App\Enum\OrderType;
use App\Models\EventClient;
use App\Models\Order;
use Illuminate\Support\Carbon;
use MetaFramework\Accessors\Countries;

trait EventVariables {

    // Add a property to track if we're in PDF mode
    protected bool $isPdfMode = false;

    // Add a method to set PDF mode
    public function setPdfMode(bool $isPdf): self
    {
        $this->isPdfMode = $isPdf;
        return $this;
    }

    public function EVENT_Type(): string
    {
        return $this->event?->type->name ?? '';
    }

    public function EVENT_Adresse(): string
    {
        return $this->event->place?->address?->text_address ?? '';
    }

    public function EVENT_Ville(): string
    {
        return $this->event->place?->address?->locality ?? '';
    }

    public function EVENT_Pays(): string
    {
        return isset($this->event->place->address->country_code) ? Countries::getCountryNameByCode($this->event->place?->address?->country_code) : '';
    }

    public function EVENT_Date_Debut(): string
    {
        return $this->event->starts ?? '';
    }

    public function EVENT_Date_Fin(): string
    {
        return $this->event->ends ?? '';
    }

    public function EVENT_Nom(): string
    {
        return $this->event->texts?->name ?? '';
    }

    public function EVENT_Acronyme(): string
    {
        return $this->event->texts?->subname ?? '';
    }

    public function EVENT_Lieu(): string
    {
        return $this->event->place ? ($this->event->place?->name . ', ' .  $this->event->place?->address?->locality . ', ' . Countries::getCountryNameByCode($this->event->place?->address?->country_code)) : '';
    }

    public function EVENT_PrenomRespInscription(): string
    {
        return $this->event->adminSubs?->first_name ?? '';
    }

    public function EVENT_NomRespInscription(): string
    {
        return $this->event->adminSubs?->last_name ?? '';
    }

    public function EVENT_EmailRespInscription(): string
    {
        return $this->event->adminSubs?->email ?? '';
    }

    public function EVENT_TelRespInscription(): string
    {
        return $this->event->adminSubs?->profile?->mobile ?? '';
    }

    public function EVENT_PrenomGrant(): string
    {
        return $this->event->pec?->grantAdmin?->first_name ?? '';
    }

    public function EVENT_NomGrant(): string
    {
        return $this->event->pec?->grantAdmin?->last_name ?? '';
    }

    public function EVENT_MobileGrant(): string
    {
        return $this->event->pec?->grantAdmin?->profile?->mobile ?? '';
    }

    public function EVENT_Clients(): string
    {
        // Get all accounts from EventClient for this event
        $clientNames = EventClient::where('event_id', $this->event->id)
            ->with('account')
            ->get()
            ->map(function ($eventClient) {
                // Get account name
                if ($eventClient->account) {
                    return trim($eventClient->account->first_name . ' ' . $eventClient->account->last_name);
                }
                return null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return $clientNames->implode(', ');
    }

    public function EVENT_BannerLarge(): string
    {
        if (!$this->event) {
            return '';
        }

        $banner = $this->getBanner($this->event, 'banner_large');

        if ($banner) {
            return $this->formatImageTag($banner, $this->event->texts?->name ?: config('app.name'));
        }

        return '';
    }

    public function EVENT_BannerMedium(): string
    {
        if (!$this->event) {
            return '';
        }

        $banner = $this->getBanner($this->event, 'banner_medium');

        if ($banner) {
            return $this->formatImageTag($banner, $this->event->texts->name);
        }

        return '';
    }

    public function EVENT_Thumbnail(): string
    {
        if (!$this->event) {
            return '';
        }

        $banner = $this->getBanner($this->event, 'thumbnail');

        if ($banner) {
            return $this->formatImageTag($banner, $this->event->texts->name);
        }

        return '';
    }

    public function EVENT_Url(): string
    {
        if(!$this->eventContact) {
            return '';
        }

        $token = AutoConnectHelper::generateAutoConnectUrlForEventContact($this->eventContact);
        return '<a href="' . $token . '">' . __('ui.auto_connect_link') . '</a>';
    }

    /**
     * Format image tag based on whether we're generating a PDF or HTML
     */
    protected function formatImageTag(string $imagePath, string $altText): string
    {
        if ($this->isPdfMode) {
            // For PDF, convert to absolute path
            $absolutePath = public_path($imagePath);

            // Check if file exists
            if (file_exists($absolutePath)) {
                // Option 1: Use absolute file path (works with most PDF generators)
                return '<img src="' . $absolutePath . '" alt="' . htmlspecialchars($altText) . '" style="max-width: 100%;" />';

                // Option 2: Convert to base64 (more reliable but uses more memory)
                // $imageData = base64_encode(file_get_contents($absolutePath));
                // $mimeType = mime_content_type($absolutePath);
                // return '<img src="data:' . $mimeType . ';base64,' . $imageData . '" alt="' . htmlspecialchars($altText) . '" style="max-width: 100%;" />';
            }

            return '';
        }

        // For HTML display, use relative URL
        return '<img src="' . $imagePath . '" alt="' . htmlspecialchars($altText) . '" style="max-width: 100%;" />';
    }
}
