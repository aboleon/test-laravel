<?php

namespace App\Services\Filters;

use App\Services\Filters\Interfaces\FilterProviderInterface;
use App\Traits\Models\EventModelTrait;
use Exception;

/**
 * Simple distributor for filter configurations
 */
class FilterParser
{
    use EventModelTrait;

    private array $filters = [];

    public function add(string $filterClass): self
    {
        if (!class_exists($filterClass)) {
            throw new Exception("Filter class {$filterClass} not found");
        }

        if (!in_array(FilterProviderInterface::class, class_implements($filterClass))) {
            throw new Exception("Filter class {$filterClass} must implement FilterProviderInterface");
        }

        $filters = $filterClass::getFilters($this);
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    public function serve(): array
    {
        $this->sortFilters();
        return $this->filters;
    }

    public function serveAsJsObject(): string
    {
        $this->sortFilters();
        return $this->arrayToJS($this->filters);
    }

    private function sortFilters(): self
    {
        usort($this->filters, fn($a, $b) => strcoll($a['label'], $b['label']));
        return $this;
    }

    private function arrayToJS($data): string
    {
        if (!is_array($data)) {
            return $this->valueToJS($data);
        }

        // Check if indexed array
        if (array_keys($data) === range(0, count($data) - 1)) {
            $items = array_map([$this, 'arrayToJS'], $data);
            return '[' . "\n" . implode(",\n", $items) . "\n" . ']';
        }

        // Associative array (JS object)
        $items = [];
        foreach ($data as $key => $value) {
            $jsValue = $this->arrayToJS($value);
            // Keys might have special characters, so always quote them if they're not simple identifiers
            if (preg_match('/^[a-zA-Z_$][a-zA-Z0-9_$]*$/', $key)) {
                $items[] = "    {$key}: {$jsValue}";
            } else {
                // Quote the key if it has special characters
                $items[] = "    '" . addslashes($key) . "': {$jsValue}";
            }
        }
        return '{' . "\n" . implode(",\n", $items) . "\n" . '}';
    }

    private function valueToJS($value): string
    {
        if (is_string($value)) {
            // If it starts with 'function', output as-is (it's JavaScript code)
            if (str_starts_with(trim($value), 'function')) {
                return $value;
            }

            // Only specific operator variables should be unwrapped
            $operatorVariables = [
                'string_operators',
                'boolean_operators',
                'date_operators',
                'select_operators',
                'non_nullable_select_operators',
                'number_operators'
            ];

            if (in_array($value, $operatorVariables)) {
                return $value; // No quotes for these specific variables
            }

            // Everything else gets wrapped in quotes
            return "'" . addslashes($value) . "'";
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        return "''"; // fallback
    }

}
