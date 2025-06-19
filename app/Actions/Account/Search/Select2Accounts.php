<?php

namespace App\Actions\Account\Search;

use App\DataTables\View\UserView;
use App\Traits\Models\AccountModelTrait;
use App\Traits\Models\EventModelTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Select2Accounts
{

    use EventModelTrait;
    use AccountModelTrait;

    public function filterAccounts(): array
    {
        $options = Arr::except(request()->all(), ['action']);

        $keyword = $options['q'] ?? null;

        $key   = $options['key'] ?? 'id';
        $value = $options['value'] ?? 'text';

        $query = UserView::query()
            ->select('id as '.$key)
            ->addSelect(DB::raw('CONCAT( first_name, " ", last_name, " (", COALESCE(locality, ""), " - ", COALESCE(country, ""), ")", " ", email ) as '.$value))
            ->searchByName($keyword)
            ->showTrashed(! empty($options['showTrashed']));

        if ( ! empty($options['exclude_event'])) {
            $this->setEvent((int)$options['exclude_event']);
            if ($this->event) {
                $query->excludeEvent($this->event->id);
            }
        }

        $this->response['results'] = $query->get()->toArray();

        return $this->fetchResponse();
    }
}
