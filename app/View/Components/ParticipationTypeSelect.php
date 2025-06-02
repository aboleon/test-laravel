<?php

namespace App\View\Components;

use App\Accessors\Dictionnaries;
use App\Enum\ParticipantType;
use Illuminate\View\Component;
use Illuminate\View\View;

class ParticipationTypeSelect extends Component
{

    public function __construct(
        public string $name,
        public ?int $affected = null
    )
    {
    }

    /**
     * Format and return the participation types.
     *
     * @return array
     */
    public function participationTypes(): array
    {
        $participationTypes = Dictionnaries::participationTypes();
        $formattedData = [];

        foreach ($participationTypes as $type => $participations) {
            $type = ParticipantType::translated($type);
            $formattedData[$type] = [
                'name' => $type,
                'values' => []
            ];

            foreach ($participations as $participation) {
                $formattedData[$type]['values'][$participation->id] = $participation->name;
            }
        }

        return $formattedData;
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.participation-type-select');
    }
}
