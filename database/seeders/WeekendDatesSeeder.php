<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Date;
use Carbon\Carbon;

class WeekendDatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Populates all weekend dates from February 7 to March 1, 2026
     */
    public function run(): void
    {
        $startDate = Carbon::create(2026, 2, 7);
        $endDate = Carbon::create(2026, 3, 1);
        
        $weekendDates = [];
        
        // Iterate through each day in the range
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Check if it's Saturday (6) or Sunday (0)
            if ($currentDate->isSaturday() || $currentDate->isSunday()) {
                $weekendDates[] = [
                    'date' => $currentDate->format('Y-m-d'),
                    'event_name' => $currentDate->format('l') . ' Night',
                    'location' => 'Main Venue',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $currentDate->addDay();
        }
        
        // Insert all weekend dates
        Date::insert($weekendDates);
        
        $this->command->info('Successfully seeded ' . count($weekendDates) . ' weekend dates from Feb 7 to March 1, 2026');
    }
}
