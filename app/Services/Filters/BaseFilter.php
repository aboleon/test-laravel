<?php

namespace App\Services\Filters;

use App\Services\Filters\Traits\FiltersTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseFilter
{
    use FiltersTrait;

    /**
     * Get the initial joined tables for this filter type
     *
     * @return array
     */
    abstract protected function getInitialJoinedTables(): array;

    /**
     * Get the base query with main table and initial joins
     *
     * @return string
     */
    abstract protected function getBaseQuery(): string;

    /**
     * Get any additional WHERE conditions that should always be applied
     *
     * @return string|null
     */
    abstract protected function getBaseWhereConditions(): ?string;

    /**
     * Build a query based on decoded JSON search filter
     *
     * @param  string  $searchFilter  JSON string containing filter rules
     *
     * @return string SQL query
     */
    public function buildQuery(string $searchFilter): string
    {
        // Decode JSON to array
        $filterData = json_decode($searchFilter, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON provided: " . json_last_error_msg());
        }

        // Normalize the structure - handle both single rule group and multiple rule groups
        if (isset($filterData['condition']) && isset($filterData['rules'])) {
            // Single rule group - wrap it in an array to normalize the structure
            $rules = [$filterData];
        } else {
            // Multiple rule groups - use as-is
            $rules = $filterData['rules'] ?? [];
        }

        // Initialize SQL parts
        $joins = [];
        $andConditions = [];
        $orConditions = [];

        // Track tables that have been joined to avoid duplicates
        $joinedTables = $this->getInitialJoinedTables();

        // Define tables that should automatically join to users via user_id
        $userLinkedTables = [
            'account_profile',
            'account_address',
            'account_phones'
        ];

        // Helper function to ensure required intermediate joins
        $ensureUserJoin = function($table) use (&$joinedTables, &$joins, $userLinkedTables) {
            if (in_array($table, $userLinkedTables) && !isset($joinedTables[$table])) {
                $joinedTables[$table] = true;
                $joins[] = "JOIN {$table} ON {$table}.user_id = users.id";
            }
        };

        // Helper function to add unique joins
        $addJoin = function($joinStatement) use (&$joins) {
            if (!in_array($joinStatement, $joins)) {
                $joins[] = $joinStatement;
            }
        };

        // Process each rule set
        foreach ($rules as $ruleSet) {
            $condition = $ruleSet['condition'];
            $subRules = $ruleSet['rules'];

            // Process rules within this set
            $setConditions = [];
            foreach ($subRules as $rule) {
                // Check if this is a nested rule
                if (isset($rule['nested']) && isset($rule['query'])) {
                    // Process nested rules - they are always combined with AND internally
                    $nestedRules = $rule['query']['rules'];
                    $nestedConditions = [];
                    $nestedTable = $rule['nested'];

                    // Check if there's a related field for this nested rule
                    $nestedRelated = isset($rule['related']) ? $rule['related'] : null;

                    // Track whether we've joined this nested table
                    $nestedTableJoined = false;

                    foreach ($nestedRules as $nestedRule) {
                        // Skip rules with empty values EXCEPT for operators that don't need values
                        $noValueOperators = ['is_null', 'is_not_null'];
                        if (!in_array($nestedRule['operator'], $noValueOperators)) {
                            if (!isset($nestedRule['value']) ||
                                ($nestedRule['value'] === '' ||
                                    $nestedRule['value'] === null ||
                                    (is_array($nestedRule['value']) && empty($nestedRule['value'])))) {
                                continue;
                            }
                        }

                        // Parse the column reference (table.column)
                        [$table, $column] = explode('.', $nestedRule['id']);

                        // Track necessary joins for nested table
                        if (!isset($joinedTables[$table]) && !$nestedTableJoined) {
                            $joinedTables[$table] = true;
                            $nestedTableJoined = true;

                            // Use the related field from the nested rule if available
                            if ($nestedRelated) {
                                $addJoin("JOIN {$table} ON {$nestedRelated}");
                            } else {
                                // Check for relation in the individual nested rule
                                if (isset($nestedRule['related'])) {
                                    $addJoin("JOIN {$table} ON {$nestedRule['related']}");
                                } else {
                                    // Default join to users if not specified
                                    if ($table !== 'users') {
                                        // Check if this is a user-linked table
                                        if (in_array($table, $userLinkedTables)) {
                                            $addJoin("JOIN {$table} ON {$table}.user_id = users.id");
                                        } else {
                                            // For other tables without specified relation, don't join automatically
                                            // You might want to throw an exception here or handle it differently
                                            continue;
                                        }
                                    }
                                }
                            }
                        }

                        // Map the operator for nested rule
                        $sqlOp = $this->mapOperatorToSql($nestedRule['operator']);

                        // Handle JSON parsing if specified
                        $fieldReference = $nestedRule['id'];
                        if (isset($nestedRule['parse']) && $nestedRule['parse'] === 'json') {
                            $fieldReference = "JSON_UNQUOTE(JSON_EXTRACT({$nestedRule['id']}, '$.fr'))";
                        }

                        // Check for custom function values in nested rules
                        if (isset($nestedRule['value']) && is_string($nestedRule['value']) && str_starts_with($nestedRule['value'], 'function_')) {
                            $customResult = $this->handleCustomFunction($nestedRule['value'], $nestedRule['operator'], $fieldReference);
                            if ($customResult) {
                                if ($customResult['type'] === 'where') {
                                    $nestedConditions[] = $customResult['query'];
                                } elseif ($customResult['type'] === 'join') {
                                    $addJoin($customResult['query']);
                                }
                            }
                            continue;
                        }

                        // Format nested condition
                        if (!$sqlOp['needs_value']) {
                            // Special handling for is_null/is_not_null on string fields
                            if (in_array($nestedRule['operator'], ['is_null', 'is_not_null'])) {
                                $nestedConditions[] = $this->formatNullCondition($fieldReference, $nestedRule['operator']);
                            } else {
                                if (isset($sqlOp['value'])) {
                                    $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$sqlOp['value']}'";
                                } else {
                                    $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']}";
                                }
                            }
                        } else {
                            // Format the value
                            $value = $nestedRule['value'];

                            // Handle null values
                            if ($value === null) {
                                $nestedConditions[] = "{$fieldReference} IS NULL";
                            } else {
                                // Special handling for value 0 with equal/not_equal operators
                                if ($value === 0 || $value === '0') {
                                    if ($nestedRule['operator'] === 'equal') {
                                        $nestedConditions[] = "({$fieldReference} = 0 OR {$fieldReference} IS NULL)";
                                    } elseif ($nestedRule['operator'] === 'not_equal') {
                                        $nestedConditions[] = "({$fieldReference} != 0 AND {$fieldReference} IS NOT NULL)";
                                    } else {
                                        // For other operators with value 0, treat normally
                                        $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']} 0";
                                    }
                                } else {
                                    // Format the value if needed
                                    if (isset($sqlOp['format'])) {
                                        $formattedValue = sprintf($sqlOp['format'], $value);
                                        // Escape quotes for string values
                                        if (is_string($value)) {
                                            $formattedValue = str_replace("'", "''", $formattedValue);
                                        }
                                        $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$formattedValue}'";
                                    } else {
                                        // For numeric values, don't add quotes
                                        if (is_numeric($value)) {
                                            $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']} {$value}";
                                        } else {
                                            // Escape quotes for string values
                                            $escapedValue = str_replace("'", "''", $value);
                                            $nestedConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$escapedValue}'";
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Combine nested conditions with AND (as specified)
                    if (!empty($nestedConditions)) {
                        $setConditions[] = '(' . implode(' AND ', $nestedConditions) . ')';
                    }

                } else {
                    // Skip rules with empty values EXCEPT for operators that don't need values
                    $noValueOperators = ['is_null', 'is_not_null'];
                    if (!in_array($rule['operator'], $noValueOperators)) {
                        if (!isset($rule['value']) ||
                            ($rule['value'] === '' ||
                                $rule['value'] === null ||
                                (is_array($rule['value']) && empty($rule['value'])))) {
                            continue;
                        }
                    }

                    // Regular rule (non-nested)
                    // Parse the column reference (table.column)
                    [$table, $column] = explode('.', $rule['id']);

                    // Track necessary joins
                    if (!isset($joinedTables[$table])) {
                        $joinedTables[$table] = true;

                        // Add relation if specified
                        if (isset($rule['related'])) {
                            $addJoin("JOIN {$table} ON {$rule['related']}");
                        } else {
                            // Default join to users if not specified
                            if ($table !== 'users') {
                                // Check if this is a user-linked table
                                if (in_array($table, $userLinkedTables)) {
                                    $addJoin("JOIN {$table} ON {$table}.user_id = users.id");
                                } else {
                                    // For other tables without specified relation, don't join automatically
                                    // You might want to throw an exception here or handle it differently
                                    continue;
                                }
                            }
                        }
                    }

                    // Map the operator
                    $sqlOp = $this->mapOperatorToSql($rule['operator']);

                    // Handle JSON parsing if specified
                    $fieldReference = $rule['id'];
                    if (isset($rule['parse']) && $rule['parse'] === 'json') {
                        $fieldReference = "JSON_UNQUOTE(JSON_EXTRACT({$rule['id']}, '$.fr'))";
                    }

                    // Check for custom function values
                    if (isset($rule['value']) && is_string($rule['value']) && str_starts_with($rule['value'], 'function_')) {
                        $customResult = $this->handleCustomFunction($rule['value'], $rule['operator'], $fieldReference);
                        if ($customResult) {
                            if ($customResult['type'] === 'where') {
                                $setConditions[] = $customResult['query'];
                            } elseif ($customResult['type'] === 'join') {
                                $addJoin($customResult['query']);
                            }
                        }
                        continue;
                    }

                    // Format condition based on operator type
                    if (!$sqlOp['needs_value']) {
                        // Special handling for is_null/is_not_null on string fields
                        if (in_array($rule['operator'], ['is_null', 'is_not_null'])) {
                            $setConditions[] = $this->formatNullCondition($fieldReference, $rule['operator']);
                        } else {
                            if (isset($sqlOp['value'])) {
                                $setConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$sqlOp['value']}'";
                            } else {
                                $setConditions[] = "{$fieldReference} {$sqlOp['operator']}";
                            }
                        }
                    } else {
                        // Format the value
                        $value = $rule['value'];

                        // Handle null values
                        if ($value === null) {
                            $setConditions[] = "{$fieldReference} IS NULL";
                        } else {
                            // Special handling for value 0 with equal/not_equal operators
                            if ($value === 0 || $value === '0') {
                                if ($rule['operator'] === 'equal') {
                                    $setConditions[] = "({$fieldReference} = 0 OR {$fieldReference} IS NULL)";
                                } elseif ($rule['operator'] === 'not_equal') {
                                    $setConditions[] = "({$fieldReference} != 0 AND {$fieldReference} IS NOT NULL)";
                                } else {
                                    // For other operators with value 0, treat normally
                                    $setConditions[] = "{$fieldReference} {$sqlOp['operator']} 0";
                                }
                            } else {
                                // Format the value if needed
                                if (isset($sqlOp['format'])) {
                                    $formattedValue = sprintf($sqlOp['format'], $value);
                                    // Escape quotes for string values
                                    if (is_string($value)) {
                                        $formattedValue = str_replace("'", "''", $formattedValue);
                                    }
                                    $setConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$formattedValue}'";
                                } else {
                                    // For numeric values, don't add quotes
                                    if (is_numeric($value)) {
                                        $setConditions[] = "{$fieldReference} {$sqlOp['operator']} {$value}";
                                    } else {
                                        // Special handling for BETWEEN operator with array values
                                        if ($rule['operator'] === 'between' && is_array($value) && count($value) === 2) {
                                            // Escape both values
                                            $escapedValue1 = str_replace("'", "''", $value[0]);
                                            $escapedValue2 = str_replace("'", "''", $value[1]);
                                            $setConditions[] = "{$fieldReference} BETWEEN '{$escapedValue1}' AND '{$escapedValue2}'";
                                        } else {
                                            // Escape quotes for string values
                                            $escapedValue = str_replace("'", "''", $value);
                                            $setConditions[] = "{$fieldReference} {$sqlOp['operator']} '{$escapedValue}'";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Combine conditions based on rule set condition
            if (!empty($setConditions)) {
                $combinedCondition = '(' . implode(" {$condition} ", $setConditions) . ')';

                if ($condition === 'AND') {
                    $andConditions[] = $combinedCondition;
                } else {
                    $orConditions[] = $combinedCondition;
                }
            }
        }

        // Build the final query
        $query = $this->getBaseQuery();

        // Sort joins to ensure dependencies are met
        // First, we need to identify join dependencies
        $sortedJoins = $this->sortJoinsByDependencies($joins);

        // Add sorted joins after base query is established
        if (!empty($sortedJoins)) {
            $query .= ' ' . implode(' ', $sortedJoins);
        }

        // Add base WHERE conditions
        $baseWhere = $this->getBaseWhereConditions();
        if ($baseWhere) {
            $query .= " WHERE {$baseWhere}";
        }

        // Create combined conditions that properly respect the grouping
        $whereConditions = [];

        // Process AND conditions
        if (!empty($andConditions)) {
            $whereConditions = array_merge($whereConditions, $andConditions);
        }

        // Process OR conditions - they should be grouped together
        if (!empty($orConditions)) {
            // If we have OR conditions, group them together
            $whereConditions[] = '(' . implode(' OR ', $orConditions) . ')';
        }

        // Combine all conditions with AND (since top-level groups should be ANDed)
        if (!empty($whereConditions)) {
            $connector = $baseWhere ? " AND " : " WHERE ";
            $query .= $connector . implode(" AND ", $whereConditions);
        }

        Log::info('MultiSearch', ['query' => $query, 'filters' => $filterData]);

        return $query;
    }

    /**
     * Get filtered IDs that match the filter
     *
     * @param  string  $searchFilter  JSON string containing filter rules
     *
     * @return array Array of IDs
     */
    public function getFilteredIds(string $searchFilter): array
    {
        return array_column(
            DB::select($this->buildQuery($searchFilter)),
            'id',
        );
    }

    /**
     * Sort joins by their dependencies to ensure proper order
     *
     * @param array $joins Array of JOIN statements
     * @return array Sorted array of JOIN statements
     */
    protected function sortJoinsByDependencies(array $joins): array
    {
        // Define the base tables that need to be joined first
        $baseTables = ['account_profile', 'account_address', 'account_phones'];
        $sortedJoins = [];
        $remainingJoins = [];

        // First, separate base table joins from dependent joins
        foreach ($joins as $join) {
            $isBaseJoin = false;
            foreach ($baseTables as $baseTable) {
                if (preg_match("/JOIN\s+{$baseTable}\s+ON\s+{$baseTable}\.user_id/", $join)) {
                    $isBaseJoin = true;
                    break;
                }
            }

            if ($isBaseJoin) {
                $sortedJoins[] = $join;
            } else {
                $remainingJoins[] = $join;
            }
        }

        // Add remaining joins after base joins
        return array_merge($sortedJoins, $remainingJoins);
    }

    /**
     * Handle custom function values (like function_createdFront)
     *
     * @param string $functionValue The function value (e.g., "function_createdFront")
     * @param string $operator The operator being used
     * @param string $fieldReference The field reference
     * @return array|null Array with 'type' and 'query', or null if method doesn't exist
     */
    protected function handleCustomFunction(string $functionValue, string $operator, string $fieldReference): ?array
    {
        // Extract method name from function_methodName
        $methodName = substr($functionValue, strlen('function_'));

        // Check if the method exists in the current class
        if (method_exists($this, $methodName)) {
            return $this->$methodName($operator, $fieldReference);
        }

        return null;
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
