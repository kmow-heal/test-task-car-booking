<?php

namespace App\Http\Controllers;

use App\Services\FreeDaysService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarBookingController extends Controller
{
    public function __construct(
        protected FreeDaysService $freeDaysService
    ){

    }

    public function index(Request $request){
        $start_date = $request->has('start_date') ? 
        $request->input('start_date') : Carbon::now()->startOfMonth()->format('Y-m-d');
        $end_date = $request->has('end_date') ? 
        $request->input('end_date') : Carbon::now()->endOfMonth()->format('Y-m-d');

        $company_id = $request->has('company') ? $request->input('company') : 1;

        $lang = $request->has('lang') ? $request->input('lang') : 'en';

        $rc_bookings = 'rc_bookings';
        $rc_cars = 'rc_cars';
        $rc_cars_models = 'rc_cars_models';
        $rc_cars_models_translations = 'rc_cars_models_translations';

        $timezone = '+2:00';

        $down_hour = 9;
        $up_hour = 21;
        $hour_for_free = 9;

        $start_period = Carbon::parse($start_date)->setTime($down_hour, 0, 0);
        $end_period = Carbon::parse($end_date)->setTime($up_hour, 0, 0);

        $bookings = DB::table($rc_bookings)
        ->join($rc_cars, $rc_cars . '.car_id', '=', $rc_bookings . '.car_id')
        ->join($rc_cars_models, $rc_cars. '.car_model_id', '=', $rc_cars_models . '.car_model_id')
        ->join($rc_cars_models_translations, $rc_cars_models. '.car_model_id', '=', $rc_cars_models_translations . '.car_model_id')
        ->where($rc_bookings.'.end_date', '>', $start_date)
        ->where($rc_bookings.'.start_date', '<', $end_date)
        ->where($rc_bookings.'.status', '=', '1')
        ->where($rc_bookings.'.company_id', '=', $company_id)
        ->where( $rc_bookings.'.is_deleted', '!=', '1')
        ->where( $rc_cars.'.status', '=', '1')
        ->where($rc_cars_models_translations.'.lang', '=', $lang)
        ->where($rc_bookings.'.car_id','=', '3759')
        ->orderBy( $rc_bookings.'.car_id')
        ->orderBy( $rc_bookings.'.start_date')
        ->get();

        // Set values for calc free days
        $this->freeDaysService->setDownHour($down_hour);
        $this->freeDaysService->setUpHour($up_hour);
        $this->freeDaysService->setStartPeriod($start_period);
        $this->freeDaysService->setEndPeriod($end_period);
        $this->freeDaysService->setHoursForFree($hour_for_free);
        
        $bussy_datatimes = [];
        $free_days = [];
        foreach($bookings as $b){
            // Create Carbon data for work
            $bussy_datatimes[$b->car_id][] = (object) [
                "start_date" => Carbon::parse($b->start_date)->setTimezone($timezone),
                "end_date" => Carbon::parse($b->end_date)->setTimezone($timezone),
            ];

            // Write data about car
            if(!array_key_exists($b->car_id, $free_days)){
                $free_days[$b->car_id]['busy'] = 0;
                $free_days[$b->car_id]['service'] = 0;
                $free_days[$b->car_id]['name'] = $b->name;
                $free_days[$b->car_id]['year'] = $b->attribute_year;
                $free_days[$b->car_id]['color'] = $b->attribute_interior_color;
                $free_days[$b->car_id]['number'] = $b->registration_number;
            }
        }
        // dd($bussy_datatimes['5122'][10]);
        // calc free days for each car
        foreach($bussy_datatimes as $car_id => $d){
            $days = $this->freeDaysService->calculateFreeDaysFromBusy($d);
            $free_days[$car_id]['free'] = $days;
            $free_days[$car_id]['all'] = $days;
        }

        // get other data from db for render page
        $companies = DB::table($rc_bookings)->distinct()->select('company_id')->orderBy('company_id')->get();
        $languages = DB::table($rc_cars_models_translations)->distinct()->select('lang')->orderBy('lang')->get();
        
        return view('cars_booking.index', [
            'bookings' => $bookings,
            'companies' => $companies,
            'cars' => $free_days,
            'langs' => $languages,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'select_company' => $company_id,
            'select_lang' => $lang
        ]);
    }

}
