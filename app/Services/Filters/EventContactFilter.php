<?php

namespace App\Services\Filters;

use App\Traits\Models\EventModelTrait;

class EventContactFilter extends BaseFilter
{
    use EventModelTrait;

    /**
     * Get the initial joined tables for this filter type
     *
     * @return array
     */
    protected function getInitialJoinedTables(): array
    {
        return ['events_contacts' => true, 'users' => true];
    }

    /**
     * Get the base query with main table and initial joins
     *
     * @return string
     */
    protected function getBaseQuery(): string
    {
        return "SELECT DISTINCT events_contacts.id FROM events_contacts" .
            " JOIN users ON events_contacts.user_id = users.id";
    }

    /**
     * Get any additional WHERE conditions that should always be applied
     *
     * @return string|null
     */
    protected function getBaseWhereConditions(): ?string
    {
        return "events_contacts.event_id = {$this->event->id}";
    }

    /**
     * Get event contact IDs that match the filter
     *
     * @param  string  $searchFilter  JSON string containing filter rules
     *
     * @return array Array of contact IDs
     */
    public function getFilteredContactIds(string $searchFilter): array
    {
        return $this->getFilteredIds($searchFilter);
    }
}
