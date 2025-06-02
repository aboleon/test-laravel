<?php


namespace App\Http\Controllers;


use App\Actions\EventManager\Program\OratorsAction;
use App\Actions\EventManager\Program\ProgramInterventionActions;
use App\Actions\Hotels\HotelJsonAction;
use App\Helpers\CaseHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ApiController extends Controller
{
    public function json(Request $request)
    {
        $action = CaseHelper::dashToCamel($request->input('action'));

        try {
            if (method_exists($this, $action) && $action !== 'json') {
                $arr = call_user_func([$this, $action]);
                return response()->json($arr);
            } else {
                return response()->json(['message' => "Action not found"], 404);
            }
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function jsonDoc(): View{
        return view('front.api.json-api.readme');
    }

    //--------------------------------------------
    // Program
    //--------------------------------------------
    protected function interventionsBySession(): array
    {
        return (new ProgramInterventionActions())->getInterventionsBySession(request('id'));
    }

    protected function interventionsByContainer(): array
    {
        return (new ProgramInterventionActions())->getInterventionsByContainer(request('id'));
    }

    protected function interventionsByEvent(): array
    {
        return (new ProgramInterventionActions())->getInterventionsByEvent(request('id'));
    }

    //--------------------------------------------
    // Hotel
    //--------------------------------------------
    protected function hotel(): array
    {
        return (new HotelJsonAction())->getHotelById(request('id'));
    }

    protected function hotelByEvent(): array
    {
        return (new HotelJsonAction())->getHotelByEvent(request('event_id'), request('hotel_id'));
    }


    //--------------------------------------------
    // Orators
    //--------------------------------------------
    protected function oratorsByIntervention(): array
    {
        return (new OratorsAction())->getOratorsByIntervention(request('id'));
    }

    protected function oratorsBySession(): array
    {
        return (new OratorsAction())->getOratorsBySession(request('id'));
    }

    protected function oratorsByContainer(): array
    {
        return (new OratorsAction())->getOratorsByContainer(request('id'));
    }

    protected function oratorsByEvent(): array
    {
        return (new OratorsAction())->getOratorsByEvent(request('id'));
    }
}
