<?php

namespace App\Actions\Account;

use App\Actions\AccountProfile\View\AccountProfileExportView;
use App\Actions\Export\BaseExportAction;
use App\Helpers\CsvHelper;
use App\Models\EventContact;
use DateTime;
use MetaFramework\Traits\Responses;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Throwable;

class ExportAccountProfilesWrapperAction
{
    use Responses;

    public function exportAccountProfiles(): array
    {
        $userIds = $this->getSaneIds();
        return $this->exportAccountProfilesByUserIds($userIds);
    }

    public function exportAccountProfilesByEventContacts(): array
    {
        $eventContactIds = $this->getSaneIds();
        $userIds = EventContact::whereIn('id', $eventContactIds)
            ->pluck('user_id')
            ->toArray();

        return $this->exportAccountProfilesByUserIds($userIds);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getSaneIds(): array{
        return CsvHelper::csvToUniqueArray(request('ids'));
    }

    private function exportAccountProfilesByUserIds(array $userIds): array
    {
        try {
            $fields = request('exportFields');

            $action = new ExportAccountProfilesAction($userIds, $fields);
            $content = $action->exportToXlsx();

            $this->responseElement('file', base64_encode($content));
            $filename = 'export_' . (new DateTime())->format('Y-m-d_H-i-s') . '.xlsx';
            $this->responseElement('filename', $filename);
            return $this->fetchResponse();

        } catch (Throwable $e) {
            $this->responseException($e);
            return $this->fetchResponse();
        }
    }
}