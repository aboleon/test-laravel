<?php

namespace App\Actions\Account\Search;

use App\Enum\SavedSearches;
use App\Models\AdvancedSearch;
use App\Models\AdvancedSearchFilter;
use App\Models\SavedSearch;
use App\Services\Filters\AccountFilter;
use App\Services\Filters\EventContactFilter;
use App\Services\Filters\GroupFilter;
use DB;
use Exception;
use InvalidArgumentException;
use MetaFramework\Traits\Responses;

class SavedSearchAction
{

    use Responses;

    public function storeCurrentSearchInSession(string $type, string $searchFilter): array
    {
        $this->enableAjaxMode();
        if (empty($searchFilter)) {
            $this->responseError('Aucun critère de recherche');

            return $this->fetchResponse();
        }

        // Validate JSON format
        $filterData = json_decode($searchFilter, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->responseError('Format de critères invalide: '.json_last_error_msg());

            return $this->fetchResponse();
        }

        $authId = auth()->id();

        // Store filters in database (replaces session storage)
        AdvancedSearchFilter::storeFilters($type, $searchFilter);

        // Execute search query and store results in database using match
        $this->performAndStoreQuery($type, $searchFilter);

        return ['ok' => 1];
    }

    /**
     * Store search results in the advanced_searches table
     * Each ID is stored as a separate record to allow for SQL joins
     *
     * @param  string  $type  The search type
     * @param  array   $ids   Array of result IDs
     *
     * @return void
     */
    protected function storeSearchIds(string $type, array $ids): void
    {
        // Get the authenticated user ID
        $authId = auth()->id();

        // Clear any existing records for this user and search type
        AdvancedSearch::removeStored($type);

        // Prepare data for bulk insert
        $records = array_map(fn($resultId)
            => [
            'auth_id' => $authId,
            'type'    => $type,
            'id'      => $resultId,
        ], $ids);

        // Insert all records at once for better performance
        if ( ! empty($records)) {
            DB::table('advanced_searches')->insert($records);
        }
    }

    public function storeSavedSearch(string $name, string $type, string $searchFilter, ?string $id = null): array
    {
        $this->enableAjaxMode();
        $userId = auth()->user()->id;
        if ( ! $userId) {
            throw new Exception('Utilisateur non connecté');
        }

        if ('' === trim($name)) {
            $this->responseError('Veuillez saisir un nom');

            return $this->fetchResponse();
        }

        if ($id) {
            $savedSearch = SavedSearch::find($id);
        } else {
            $savedSearch = new SavedSearch();
        }

        $savedSearch->user_id       = $userId;
        $savedSearch->name          = $name;
        $savedSearch->type          = $type;
        $savedSearch->search_filter = $searchFilter;
        $savedSearch->save();

        session([
            "savedSearch.$type" => [
                'currentId'   => $savedSearch->id,
                'currentName' => $savedSearch->name,
            ],
        ]);

        return [
            "id" => $savedSearch->id,
        ];
    }

    public function loadSavedSearch(string $id, string $type): array
    {
        $savedSearch = SavedSearch::find($id);

        if ($savedSearch) {
            AdvancedSearchFilter::storeFilters($type, $savedSearch->search_filter);
            $this->performAndStoreQuery($type, $savedSearch->search_filter);
            session([
                "savedSearch.$type" => [
                    'currentId'   => $savedSearch->id,
                    'currentName' => $savedSearch->name,
                ],
            ]);

            return ['ok' => 1];
        }
    }

    public function deleteSavedSearch(string $type, string $id = null): array
    {
        AdvancedSearchFilter::removeFilters($type);
        AdvancedSearch::removeStored($type);
        session()->forget("savedSearch.".$type);

        if ($id) {
            session()->forget("savedSearch.".$type);
            $savedSearch = SavedSearch::find($id);
            $savedSearch->delete();
        }

        return ['ok' => 1];
    }

    private function performAndStoreQuery(?string $type, string $searchFilter): void
    {
        $ids = match ($type) {
            SavedSearches::EVENT_CONTACTS->value => (new EventContactFilter())
                ->setEvent((int)request('event_id'))
                ->getFilteredContactIds($searchFilter),

            SavedSearches::CONTACTS->value => (new AccountFilter())
                ->getFilteredAccountIds($searchFilter),

            SavedSearches::GROUPS->value => (new GroupFilter())
                ->getFilteredGroupIds($searchFilter),

            default => throw new InvalidArgumentException("Unsupported search type: {$type}")
        };

        // Store the IDs in the database
        $this->storeSearchIds($type, $ids);
    }
}
