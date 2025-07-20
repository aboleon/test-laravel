<?php

namespace App\Exports;

use App\Traits\Models\EventModelTrait;
use MetaFramework\Traits\Ajax;

class SageExport
{

    use Ajax;
    use EventModelTrait;

    public function run()//: array
    {
        if ( ! request()->filled('event_id')) {
            $this->responseWarning("Vous n'avez pas sélectionné un évènement.");
        }

        $request_event_id = (int)request('event_id');
        $this->setEvent($request_event_id);

        if ( ! $this->event) {
            $this->responseWarning("Impossible de récupérer un évènement avec l'ID ".$request_event_id);
        }

        $this->event->load('sellableService.sageData', 'sellableService.group.sageData', 'accommodation.contingent.configs.sageData');

        //d($this->event->sellableService);
        $exportData = [];

        foreach ($this->event->sellableService as $article) {
            $exportData[] = [
                'nom_article'      => $article->title,
                'code_article'     => $article->getSageCode().$article->getSageReferenceValue(),
                'code_analytique'  => $article->getSageAnalyticsCode(),
                'compte_comptable' => $article->getSageAccountCode(),
                'compte_tva'       => $article->getSageVatAccount(),
            ];
        }

        d($exportData, 'exportData');
        //return $this->fetchResponse();
    }
}
