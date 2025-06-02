<?php

namespace App\Http\Controllers;

use App\Actions\Pdf\PdfHubAction;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PdfHubController extends Controller
{

    public function printPdf(): Response|View
    {

        $type = request('type', 'pdf');
        $identifier = request('identifier');

        if (null === $identifier) {
            return new Response("No identifier provided", 400);
        }

        $viewPath = $identifier;

        if ('html' === $type) {
            return view($viewPath);
        }

        return (new PdfHubAction)->printPdf($identifier);
    }
}
