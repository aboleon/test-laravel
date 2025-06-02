<?php

namespace App\Services\Filters;

use App\Accessors\Users;

class AccountFilter extends BaseFilter
{
    /**
     * Get the initial joined tables for this filter type
     *
     * @return array
     */
    protected function getInitialJoinedTables(): array
    {
        return ['users' => true]; // Only users table, no events_contacts
    }

    /**
     * Get the base query with main table and initial joins
     *
     * @return string
     */
    protected function getBaseQuery(): string
    {
        return "SELECT DISTINCT users.id FROM users";
    }

    /**
     * Get any additional WHERE conditions that should always be applied
     *
     * @return string|null
     */
    protected function getBaseWhereConditions(): ?string
    {
        return null; // No additional base conditions for accounts
    }

    /**
     * Get account IDs that match the filter
     *
     * @param  string  $searchFilter  JSON string containing filter rules
     *
     * @return array Array of account IDs
     */
    public function getFilteredAccountIds(string $searchFilter): array
    {
        return $this->getFilteredIds($searchFilter);
    }

    protected function createdFront(string $operator, string $fieldReference): array
    {
        $adminIds = implode(',', array_keys(Users::adminUsersSelectable()));

        $query = match ($operator) {
            'equal' => "({$fieldReference} IS NULL OR {$fieldReference} NOT IN ({$adminIds}))",
            'not_equal' => "{$fieldReference} IN ({$adminIds})",
        };


        return $query ? [
            'type'  => 'where',
            'query' => $query,
        ] : [];
    }
}
