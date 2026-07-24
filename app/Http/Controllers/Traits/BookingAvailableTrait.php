<?php

namespace App\Http\Controllers\Traits;

use App\Models\Modern\ItemMeta;

trait BookingAvailableTrait
{
    public function addOrUpdateItemMeta($id, $data)
    {
        foreach ($data as $key => $value) {
            ItemMeta::updateOrCreate(
                [
                    'rental_item_id' => $id,
                    'meta_key' => $key,
                ],
                [
                    'meta_value' => $value,
                ]
            );
        }

        // return $id;
    }

    public function returncheckOutDateByModule($checkInDate, $start_time, $checkOutDate, $end_time, $module)
    {
        $checkInTimestamp = strtotime("$checkInDate $start_time");
        $checkOutTimestamp = strtotime("$checkOutDate $end_time");
        $minutesDifference = ($checkOutTimestamp - $checkInTimestamp) / 60;
        $totalHours = $minutesDifference / 60;

        $allowedModules = $this->sameDayBookingModule(); // Array of allowed module IDs

        if (in_array($module, $allowedModules) || $totalHours < 24) {
            $checkOutDate = date('Y-m-d', strtotime($checkOutDate.' + 1 day'));
        }

        return $checkOutDate;
    }

    public function returnHowManyDaysByModule($checkInDate, $start_time, $checkOutDate, $end_time, $module = 1)
    {
        $checkInTimestamp = strtotime("$checkInDate $start_time");
        $checkOutTimestamp = strtotime("$checkOutDate $end_time");
        $secondsDifference = $checkOutTimestamp - $checkInTimestamp;
        $minutesDifference = ($checkOutTimestamp - $checkInTimestamp) / 60;
        $totalHours = $minutesDifference / 60;
        $totalDays = ceil($secondsDifference / (60 * 60 * 24));
        $totalTime['totalHours'] = $totalHours;
        $totalTime['totalDays'] = $totalDays;
        $totalTime['secondsDifference'] = $secondsDifference;
        $totalTime['minutesDifference'] = $minutesDifference;

        return $totalTime;
    }

    public function sameDayBookingModule()
    {

        return [2];
    }

    public function getMissingDays($itemId)
    {

        $data = json_decode(ItemMeta::getMetaValue($itemId, 'working_hour_list')) ?? null;
        $missingDaysArray = [];

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $presentDays = [];
        if (! empty($data)) {
            foreach ($data as $item) {
                foreach ($item as $key => $value) {
                    if (strpos($key, '_open') !== false) {
                        $day = str_replace('_open', '', $key);
                        $presentDays[] = $day;
                    }
                }
            }
        } else {
            return $missingDaysArray;
        }

        $missingDays = array_diff($daysOfWeek, $presentDays);
        $missingDaysArray = array_map('ucfirst', $missingDays);

        return $missingDaysArray;
    }
}
