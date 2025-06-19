<?php

namespace App\Services\Filters\Traits;

trait FiltersTrait
{
    /**
     * Map frontend operators to SQL operators
     *
     * @param string $operator
     * @return array
     */
    protected function mapOperatorToSql(string $operator): array
    {
        $mapping = [
            'equal' => ['operator' => '=', 'needs_value' => true],
            'not_equal' => ['operator' => '!=', 'needs_value' => true],
            'less' => ['operator' => '<', 'needs_value' => true],
            'less_or_equal' => ['operator' => '<=', 'needs_value' => true],
            'greater' => ['operator' => '>', 'needs_value' => true],
            'greater_or_equal' => ['operator' => '>=', 'needs_value' => true],
            'between' => ['operator' => 'BETWEEN', 'needs_value' => true],
            'not_between' => ['operator' => 'NOT BETWEEN', 'needs_value' => true],
            'in' => ['operator' => 'IN', 'needs_value' => true],
            'not_in' => ['operator' => 'NOT IN', 'needs_value' => true],
            'begins_with' => ['operator' => 'LIKE', 'needs_value' => true, 'format' => '%s%%'],
            'not_begins_with' => ['operator' => 'NOT LIKE', 'needs_value' => true, 'format' => '%s%%'],
            'contains' => ['operator' => 'LIKE', 'needs_value' => true, 'format' => '%%%s%%'],
            'not_contains' => ['operator' => 'NOT LIKE', 'needs_value' => true, 'format' => '%%%s%%'],
            'ends_with' => ['operator' => 'LIKE', 'needs_value' => true, 'format' => '%%%s'],
            'not_ends_with' => ['operator' => 'NOT LIKE', 'needs_value' => true, 'format' => '%%%s'],
            'is_null' => ['operator' => 'IS NULL OR', 'needs_value' => false, 'value' => ''],
            'is_not_null' => ['operator' => 'IS NOT NULL AND', 'needs_value' => false, 'value' => ''],
        ];

        return $mapping[$operator] ?? ['operator' => '=', 'needs_value' => true];
    }

    /**
     * Format SQL condition for null/not null operators on string fields
     *
     * @param string $fieldReference
     * @param string $operator
     * @return string
     */
    protected function formatNullCondition(string $fieldReference, string $operator): string
    {
        if ($operator === 'is_null') {
            // For is_null: field is null OR field is empty string
            return "({$fieldReference} IS NULL OR {$fieldReference} = '')";
        } elseif ($operator === 'is_not_null') {
            // For is_not_null: field is not null AND field is not empty string
            return "({$fieldReference} IS NOT NULL AND {$fieldReference} != '')";
        }

        return '';
    }
}
