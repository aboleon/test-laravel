<?php

namespace App\Services\Filters\Traits;

trait FiltersTrait {


    /**
     * Map JS operators to SQL operators
     *
     * @param  string  $operator  The JS operator name
     *
     * @return array Returns the SQL operator and any additional parameters
     */
    protected function mapOperatorToSql(string $operator): array
    {
        return match ($operator) {
            'equal' => ['operator' => '=', 'needs_value' => true],
            'not_equal' => ['operator' => '!=', 'needs_value' => true],
            'begins_with' => ['operator' => 'LIKE', 'format' => '%s%%', 'needs_value' => true],
            'not_begins_with' => ['operator' => 'NOT LIKE', 'format' => '%s%%', 'needs_value' => true],
            'contains' => ['operator' => 'LIKE', 'format' => '%%%s%%', 'needs_value' => true],
            'not_contains' => ['operator' => 'NOT LIKE', 'format' => '%%%s%%', 'needs_value' => true],
            'ends_with' => ['operator' => 'LIKE', 'format' => '%%%s', 'needs_value' => true],
            'not_ends_with' => ['operator' => 'NOT LIKE', 'format' => '%%%s', 'needs_value' => true],
            'is_empty' => ['operator' => '=', 'value' => '', 'needs_value' => false],
            'is_not_empty' => ['operator' => '!=', 'value' => '', 'needs_value' => false],
            'is_null' => ['operator' => 'IS NULL', 'needs_value' => false],
            'is_not_null' => ['operator' => 'IS NOT NULL', 'needs_value' => false],
            'less' => ['operator' => '<', 'needs_value' => true],
            'less_or_equal' => ['operator' => '<=', 'needs_value' => true],
            'greater' => ['operator' => '>', 'needs_value' => true],
            'greater_or_equal' => ['operator' => '>=', 'needs_value' => true],
            'between' => ['operator' => 'BETWEEN', 'needs_value' => true, 'needs_two_values' => true],
            default => throw new \InvalidArgumentException("Unsupported operator: {$operator}"),
        };
    }
}
