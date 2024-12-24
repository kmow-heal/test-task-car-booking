<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CarBookingController extends Controller
{
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

        $timezone = '-2';

        $down_hour = 9;
        $up_hour = 21;

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
        ->orderBy( $rc_bookings.'.car_id')
        ->orderBy( $rc_bookings.'.start_date')
        ->get();

        $booking_orig = clone $bookings; 

        $free_days = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse( $booking->start_date)->setTimezone($timezone);
            $end = Carbon::parse( $booking->end_date)->setTimezone($timezone);

            if(!array_key_exists($booking->car_id, $free_days)){
                $free_days[$booking->car_id]['busy'] = 0;
                $free_days[$booking->car_id]['service'] = 0;
                $free_days[$booking->car_id]['name'] = $booking->name;
                $free_days[$booking->car_id]['year'] = $booking->attribute_year;
                $free_days[$booking->car_id]['color'] = $booking->attribute_interior_color;
                $free_days[$booking->car_id]['number'] = $booking->registration_number;
            }

            if($start->lt($start_period)){
                $start = clone $start_period;
            }
            if($end->gt($end_period)){
                $end = clone $end_period;
            }
            if($start->hour < $down_hour){
                $start->setTime( $down_hour, 0, 0);
            }
            if($start->hour > $up_hour || $start->hour === $up_hour && $start->minute > 0){
                $start->setTime($up_hour, 0, 0);
            }
            if($end->hour < $down_hour){
                $end->setTime( $down_hour, 0, 0);
            }
            if($end->hour > $up_hour || $end->hour === $up_hour && $end->minute > 0){
                $end->setTime($up_hour, 0, 0);
            }

            $normal_start = clone $start;
            $normal_end = clone $end;
            $days_ab = (int)ceil(($normal_start->startOfDay())->diffInDays($normal_end->startOfDay())) + 1;
            $days = $days_ab;
            if($start->hour - 9 >= $down_hour ){
                $days--;
            }
            if($end->hour + 9 <= $up_hour ){
                $days--;
            }
            
            $days = $days > 0 ? $days : 0;

            $free_days[$booking->car_id]['busy'] += $days;

            if(preg_match('/service/', preg_quote($booking->source))){
                $days = $days_ab;
                if($start->hour - 9 >= $down_hour ){
                    $days--;
                }
                if($end->hour + 9 <= $up_hour ){
                    $days--;
                }
                
                $days = $days > 0 ? $days : 0;
                $free_days[$booking->car_id]['service'] += $days;
            }    
            
        }  
        $all = (int)ceil($start_period->diffInDays($end_period));
        $free_days = array_map(function($car) use($all){
            $car['busy'] -= $car['service'];
            $car['free'] = $all - $car['busy'] - $car['service'];
            $car['all'] = $all;
            return $car;
        }, $free_days);

        $companies = DB::table($rc_bookings)->distinct()->select('company_id')->orderBy('company_id')->get();
        $languages = DB::table($rc_cars_models_translations)->distinct()->select('lang')->orderBy('lang')->get();
        
        return view('cars_booking.index', [
            'bookings' => $booking_orig,
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
