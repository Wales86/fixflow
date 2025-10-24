<?php

namespace App\Services;

use App\Dto\TimeTracking\CreateTimeEntryData;
use App\Dto\TimeTracking\TimeEntryData;
use App\Dto\TimeTracking\UpdateTimeEntryData;
use App\Models\TimeEntry;

class TimeEntryService
{
    public function store(CreateTimeEntryData $data): TimeEntryData
    {
        $durationMinutes = $data->duration_hours_input * 60 + $data->duration_minutes_input;

        $timeEntry = TimeEntry::create([
            'repair_order_id' => $data->repair_order_id,
            'mechanic_id' => $data->mechanic_id,
            'duration_minutes' => $durationMinutes,
            'description' => $data->description,
        ]);

        $timeEntry->load('mechanic');

        return TimeEntryData::from($timeEntry);
    }

    public function update(TimeEntry $timeEntry, UpdateTimeEntryData $data): TimeEntryData
    {
        $durationMinutes = $data->duration_hours_input * 60 + $data->duration_minutes_input;

        $timeEntry->update([
            'repair_order_id' => $data->repair_order_id,
            'mechanic_id' => $data->mechanic_id,
            'duration_minutes' => $durationMinutes,
            'description' => $data->description,
        ]);

        $timeEntry->load('mechanic');

        return TimeEntryData::from($timeEntry);
    }

    public function delete(TimeEntry $timeEntry): void
    {
        $timeEntry->delete();
    }
}
