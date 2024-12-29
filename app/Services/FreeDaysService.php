<?php

namespace App\Services;

use Carbon\Carbon;

class FreeDaysService
{
    private int $up_hour = 21;
    private int $down_hour = 9;

    private int $hours_for_free = 9;

    private Carbon $start_period;
    private Carbon $end_period;

    public function calculateFreeDaysFromBusy(array $busy_datetimes)
    {
        
        $intervals = $this->selectFreeIntervals($busy_datetimes);
        $free_days = 0;
        foreach ($intervals as $interval) {
            $free_days += $this->getDiffInDays( $interval->start_date, $interval->end_date);
        }
        $free_days--;
        return $free_days > 0 ? $free_days : 0;
    }

    public function normalizationDate(Carbon &$start, Carbon &$end){
        if($start->lt($this->start_period)){
            $start = clone $this->start_period;
        }
        if($end->gt($this->end_period)){
            $end = clone $this->end_period;
        }
    }

    public function normalizationTime(Carbon &$start, Carbon &$end){
        if($start->hour < $this->down_hour){
            $start->setTime( $this->down_hour, 0, 0);
        }
        if($start->hour > $this->up_hour || $start->hour === $this->up_hour && $start->minute > 0){
            $start->setTime($this->up_hour, 0, 0);
        }
        if($end->hour < $this->down_hour){
            $end->setTime( $this->down_hour, 0, 0);
        }
        if($end->hour > $this->up_hour || $end->hour === $this->up_hour && $end->minute > 0){
            $end->setTime($this->up_hour, 0, 0);
        }
    }

    public function selectFreeIntervals(array $busy_datetime ){
        $intervals = [];
        $prev_date = $this->start_period;
        foreach($busy_datetime as $d){
            $this->normalizationDate($d->start_date, $d->end_date);
            $this->normalizationTime($d->start_date, $d->end_date);
            $intervals[] = (object) [
                "start_date" => $prev_date,
                "end_date" => $d->start_date,
            ];
            $prev_date = $d->end_date;
        }
        $intervals[] = (object) [
            "start_date" => $prev_date,
            "end_date" => $this->end_period,
        ];
        return $intervals;
    }

    public function getDiffInDays(Carbon $start, Carbon $end){
        $start_c = clone $start;
        $end_c = clone $end;
        $days = (int)ceil(($start_c->startOfDay())->diffInDays($end_c->startOfDay()))+1;
        print($days);
        $first = $this->checkFirstDayIsFree($start);
        $last = $this->checkLastDayIsFree($end);
        if($days === 1 && !($first || $last)) {
            $days = 1;
        }
        else{
            $days = $first ? $days : $days - 1;
            $days = $last ? $days : $days - 1;
        }

        return $days;
    }

    public function checkFirstDayIsFree(Carbon $start){
        return $start->hour + $this->hours_for_free < $this->up_hour || ($start->hour + $this->hours_for_free == $this->up_hour && $start->minute <= 0);
    }

    public function checkLastDayIsFree(Carbon $end){
        return $end->hour - $this->hours_for_free >= $this->down_hour;
    }

    public function setUpHour(int $up_hour){
        $this->up_hour = $up_hour;
    }

    public function setDownHour(int $down_hour){
        $this->down_hour = $down_hour;
    }

    public function setStartPeriod(Carbon $start_period){
        $this->start_period = $start_period;
    }

    public function setEndPeriod(Carbon $end_period){
        $this->end_period = $end_period;
    }

    public function setHoursForFree(int $hours_for_free){
        $this->hours_for_free = $hours_for_free;
    }
}
