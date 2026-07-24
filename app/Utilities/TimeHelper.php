<?php

namespace App\Utilities;

class TimeHelper
{
    public static function generateTimeOptions($isAM = true, $selectedValue = null)
    {
        $times = [];
        $startHour = $isAM ? 0 : 12;
        $endHour = $isAM ? 11 : 23;
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            foreach (['00', '30'] as $minute) {
                $displayHour = $hour % 12 === 0 ? 12 : $hour % 12;
                $suffix = $isAM ? 'AM' : 'PM';
                $timeValue = str_pad($displayHour, 2, '0', STR_PAD_LEFT).":$minute $suffix";
                $times[$timeValue] = $timeValue;
            }
        }

        // Optionally mark a time as selected
        // if (!is_null($selectedValue) && isset($times[$selectedValue])) {
        //     $times[$selectedValue] .= ' selected';
        // }
        return $times;
    }

    public static function generateHoursList()
    {
        $hoursList = [];

        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $hoursList[] = sprintf('%02d:%02d', $hour, $minute);
            }
        }

        return $hoursList;
    }
}
