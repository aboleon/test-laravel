<?php

namespace App\Services\Filters;

class GroupFilter extends BaseFilter
{
    /**
     * Get the initial joined tables for this filter type
     *
     * @return array
     */
    protected function getInitialJoinedTables(): array
    {
        return ['groups' => true]; // Only groups table initially
    }

    /**
     * Get the base query with main table and initial joins
     *
     * @return string
     */
    protected function getBaseQuery(): string
    {
        return "SELECT DISTINCT groups.id FROM groups";
    }

    /**
     * Get any additional WHERE conditions that should always be applied
     *
     * @return string|null
     */
    protected function getBaseWhereConditions(): ?string
    {
        return null; // No additional base conditions for groups
    }

    /**
     * Get group IDs that match the filter
     *
     * @param  string  $searchFilter  JSON string containing filter rules
     *
     * @return array Array of group IDs
     */
    public function getFilteredGroupIds(string $searchFilter): array
    {
        return $this->getFilteredIds($searchFilter);
    }
}
