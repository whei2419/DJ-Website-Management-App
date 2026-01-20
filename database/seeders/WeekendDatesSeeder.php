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
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $currentDate->addDay();
        }
        
        // Filter out dates that already exist to avoid duplicates
        if (count($weekendDates) > 0) {
            $existing = Date::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->pluck('date')
                ->map(function ($d) {
                    return (new \Carbon\Carbon($d))->format('Y-m-d');
                })->toArray();

            $toInsert = array_filter($weekendDates, function ($item) use ($existing) {
                return !in_array($item['date'], $existing);
            });

            if (count($toInsert) > 0) {
                Date::insert(array_values($toInsert));
                $this->command->info('Successfully seeded ' . count($toInsert) . ' weekend dates from Feb 7 to March 1, 2026');
            } else {
                $this->command->info('Weekend dates already seeded for Feb 7 to March 1, 2026');
            }
        } else {
            $this->command->info('No weekend dates to seed');
        }
    }
}
