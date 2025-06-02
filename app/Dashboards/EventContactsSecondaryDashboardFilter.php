<?php

namespace App\Dashboards;

use App\Enum\OrderCartType;
use App\Enum\OrderClientType;
use App\Enum\OrderStatus;
use App\Enum\OrderType;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Facades\DB;
use MetaFramework\Traits\Responses;

class EventContactsSecondaryDashboardFilter
{

    use EventModelTrait;
    use Responses;

    private array $ids = [];

    public function __construct(public readonly string $filter) {}

    public function run(): self
    {
        if ( ! method_exists($this, $this->filter)) {
            $this->responseError("Ce filtre n'est pas défini.");
        }

        $this->ids = $this->{$this->filter}();

        return $this;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    protected function accommodation(): array
    {
        $query = "SELECT DISTINCT ec.id
            FROM events_contacts ec
                     JOIN orders o ON ec.user_id = o.client_id AND ec.event_id = o.event_id
                     JOIN order_cart_accommodation oca ON oca.order_id = o.id
            WHERE ec.event_id = ?
              AND o.client_type != ?
              AND o.type = ?

            UNION

            SELECT DISTINCT oa.event_contact_id
            FROM order_attributions oa
                     JOIN orders o ON oa.order_id = o.id
            WHERE o.event_id = ?
              AND o.type = ?
              AND oa.shoppable_type = ?
            ";
        $results = DB::select(
            $query,
            [
                $this->event->id,
                OrderClientType::GROUP->value,
                OrderType::ORDER->value,
                $this->event->id,
                OrderType::ORDER->value,
                OrderCartType::ACCOMMODATION->value,
            ],
        );

        return ! empty($results) ? array_column($results, 'id') : [];
    }

    /**
     * Get unique events_contacts IDs without any accommodation records
     *
     * @return array
     */
    protected function noAccommodation(): array
    {
        // First, get all events_contacts for this event
        $query1 = "
        SELECT DISTINCT ec.id
        FROM events_contacts ec
        WHERE ec.event_id = ?
    ";

        $query2 = "
        SELECT DISTINCT ec.id
        FROM events_contacts ec
        JOIN orders o ON ec.user_id = o.client_id AND ec.event_id = o.event_id
        JOIN order_cart_accommodation oca ON oca.order_id = o.id
        WHERE ec.event_id = ?
          AND o.client_type != ?
          AND o.type = ?

        UNION

        SELECT DISTINCT oa.event_contact_id as id
        FROM order_attributions oa
        JOIN orders o ON oa.order_id = o.id
        WHERE o.event_id = ?
          AND o.type = ?
          AND oa.shoppable_type = ?
    ";

        // Get all events_contacts
        $allContacts   = DB::select($query1, [$this->event->id]);
        $allContactIds = array_column($allContacts, 'id');

        // Get contacts with accommodation
        $withAccommodation    = DB::select(
            $query2,
            [
                $this->event->id,
                OrderClientType::GROUP->value,
                OrderType::ORDER->value,
                $this->event->id,
                OrderType::ORDER->value,
                OrderCartType::ACCOMMODATION->value,
            ],
        );
        $withAccommodationIds = array_column($withAccommodation, 'id');

        // Return contacts without accommodation (difference between all contacts and those with accommodation)
        return array_values(array_diff($allContactIds, $withAccommodationIds));
    }

    protected function paid(): array
    {
        return $this->orderByPaidStatus(OrderStatus::PAID->value);
    }

    protected function unpaid(): array
    {
        return $this->orderByPaidStatus(OrderStatus::UNPAID->value);
    }

    protected function frontmade(): array
    {
        $query   = "SELECT DISTINCT ec.id
                FROM events_contacts ec
                         JOIN event_contact_tokens o ON ec.id = o.event_contact_id
                WHERE ec.event_id = ?

                ";
        $results = DB::select(
            $query,
            [
                $this->event->id,
            ],
        );

        return ! empty($results) ? array_column($results, 'id') : [];
    }

    private function orderByPaidStatus(string $status): array
    {
        $query = "SELECT DISTINCT ec.id
            FROM events_contacts ec
                     JOIN orders o ON ec.user_id = o.client_id
                                  AND ec.event_id = o.event_id
            WHERE ec.event_id = ?
              AND o.client_type != ?
              AND o.type = ?
              AND o.status = ?";

        $results = DB::select(
            $query,
            [
                $this->event->id,
                OrderClientType::GROUP->value,
                OrderType::ORDER->value,
                $status,
            ],
        );

        return ! empty($results) ? array_column($results, 'id') : [];
    }

    /**
     * Get unique events_contacts IDs with service records but NO accommodation records
     *
     * @return array
     */
    protected function servicesOnly()
    {
        // Get IDs without accommodation using the existing method
        $noAccommodationIds = $this->noAccommodation();

        // If there are no contacts without accommodation, return empty array
        if (empty($noAccommodationIds)) {
            return [];
        }

        // Create placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($noAccommodationIds), '?'));

        // Query to find contacts with services among those without accommodation
        $query = "
    SELECT DISTINCT ec.id
    FROM events_contacts ec
    JOIN orders o ON ec.user_id = o.client_id AND ec.event_id = o.event_id
    JOIN order_cart_service ocs ON ocs.order_id = o.id
    WHERE ec.event_id = ?
    AND o.client_type != ?
    AND o.type = ?
    AND ec.id IN ($placeholders)

    UNION

    SELECT DISTINCT oa.event_contact_id as id
    FROM order_attributions oa
    JOIN orders o ON oa.order_id = o.id
    WHERE o.event_id = ?
    AND o.type = ?
    AND oa.shoppable_type = ?
    AND oa.event_contact_id IN ($placeholders)
";

        // Prepare parameters
        $params = [
            $this->event->id,
            OrderClientType::GROUP->value,
            OrderType::ORDER->value,
        ];

        // Add noAccommodation IDs for the first part
        $params = array_merge($params, $noAccommodationIds);

        // Add the remaining parameters
        $params = array_merge($params, [
            $this->event->id,
            OrderType::ORDER->value,
            OrderCartType::SERVICE->value,
        ]);

        // Add noAccommodation IDs again for the second part
        $params = array_merge($params, $noAccommodationIds);

        // Execute the query
        $results = DB::select($query, $params);

        return !empty($results) ? array_column($results, 'id') : [];
    }
}
