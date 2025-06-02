<?php

namespace App\Printers\PDF;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ExampleModel
{
    private \Barryvdh\DomPDF\Pdf $pdf;

    public function __construct(
    )
    {
        $this->pdf = Pdf::loadView('pdf.example', ['data' => 'smthg']);
    }

    public function __invoke(): Response
    {
        return $this->stream();
    }

    public function stream(): Response
    {
        return $this->pdf->stream();
    }

    public function output(): string
    {
        return $this->pdf->output();
    }
}
