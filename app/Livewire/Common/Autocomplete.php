<?php

namespace App\Livewire\Common;

use Exception;
use Livewire\Component;

class Autocomplete extends Component
{
    // exposed properties
    public string $namespace = "autocomplete";
    public string $name = "input-autocomplete";
    public int|string|null $initialValue = null;
    public ?string $initialLabel = null;
    public string $mode = "static";
    public string $modelClass;
    public string $getItemsMethod;
    public array $getItemsArgs = ['input'];

    // internal props
    public string $input = "";
    public array $options = [];
    public bool $showResults = false;
    public bool $showResultsOnClick = false;

    // Add this property to store the selected value
    public $selectedValue = null;

    protected $listeners = [
        'hideDropdown' => 'hideDropdown',
        'selectedOption' => 'selectedOption',
    ];
    protected int $startIndex = 0;

    public function mount()
    {
        $this->loadOptions();

        $this->selectedValue = $this->initialValue;

        // If we have an initial value, we need to set the input text
        if (!empty($this->initialValue)) {
            if ($this->initialLabel) {
                // Use the provided label if available
                $this->input = (string)$this->initialLabel;
            } else {
                // Otherwise, find the label from the options
                $key = (string)$this->initialValue;
                if (isset($this->options[$key])) {
                    $this->input = $this->options[$key];
                }
            }
        } else {
            $this->input = "";
        }
    }

    public function render()
    {
        return view('livewire.partials.autocomplete');
    }

    public function updatedInput($value)
    {
        $this->loadOptions();
        $this->showResults = count($this->options) > 0;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function loadOptions(): void
    {
        switch ($this->mode) {
            case "static":
                if (method_exists($this->modelClass, $this->getItemsMethod)) {
                    $args = array_map(function ($arg) {
                        if ($arg == 'input') {
                            return $this->input;
                        }
                        return $arg;
                    }, $this->getItemsArgs);
                    $this->options = call_user_func([$this->modelClass, $this->getItemsMethod], ...$args);
                }
                break;
            default:
                throw new Exception("Invalid Livewire autocomplete mode");
        }
    }

    public function hideDropdown(): void
    {
        $this->showResults = false;
    }

    public function selectedOption($id, $value): void
    {
        if ($id === $this->id()) {
            $this->input = $this->options[$value];
            $this->selectedValue = $value;
            $this->dispatch($this->namespace . ".selected", $value);
        }
    }
}
