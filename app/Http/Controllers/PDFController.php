<?php

namespace App\Http\Controllers;
use App\Accessors\Accounts;
use App\Helpers\CsvHelper;
use App\MailTemplates\Models\MailTemplate;
use App\MailTemplates\PdfPrinter;
use App\MailTemplates\Templates\Courrier;
use App\Models\Event;
use App\Models\EventContact;
use iio\libmergepdf\Merger;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use MetaFramework\Traits\Responses;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Throwable;

class PDFController extends Controller
{
    use Responses;
    public function distribute(string $type, string $identifier)
    {

        $printer = '\App\Printers\PDF\\'.ucfirst($type);

        if(class_exists($printer)) {
            return (new $printer($identifier))();
        } else {
            abort(404,"PDF inconnu.");
        }

    }

    public function streamMergedPdf(): Response|View|array
    {
        $userIds = CsvHelper::csvToUniqueArray(request('contact_ids', ''));

        $event = Event::find(request('event_id'));

        if(!request('mailtemplate_id')){
            return response("<h3 style='text-align:center;margin-top:50px;'>Veuillez sélectionner un courrier.</h3>", 400);
        }

        $mailtemplate = MailTemplate::find(request('mailtemplate_id'));

        $merger = new Merger();

        foreach($userIds as $user_id){
            $event_contact = EventContact::find($user_id)->load(['account', 'event']);
            try{
                $accountAccessor = new Accounts($event_contact->account);

                App::setLocale($accountAccessor->getLocale());

                $pdfParsed = (new Courrier($event, $mailtemplate, $event_contact))->serve();
                $pdf = (new PdfPrinter($pdfParsed))->output();
                $merger->addRaw($pdf);
            } catch (Throwable $e) {
                return response("<h3 style='text-align:center;margin-top:50px;'>Erreur lors du traitement du contact #{$user_id}</h3>", 500);
            }
        }

        $mergedPdf = $merger->merge();

        App::setLocale(config('app.locale'));

        return response($mergedPdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="'. Str::slug($mailtemplate->subject) . '.pdf"');
    }
}
