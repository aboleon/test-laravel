<?php

namespace App\Exports\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ExportTrait
{
    public function exportToXlsx(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->prepareSheet($sheet);

        $writer = new Xlsx($spreadsheet);


        $stream = fopen('php://temp', 'r+');
        $writer->save($stream);
        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);
        return $content;
    }

    /**
     * Export data to CSV format
     */
    public function exportToCsv(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $this->prepareSheet($sheet);

        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(';');
        $writer->setEnclosure('"');
        $writer->setUseBOM(true);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $content ?: '';
    }
}
