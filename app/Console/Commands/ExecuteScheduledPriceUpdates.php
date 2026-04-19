<?php

namespace App\Console\Commands;

use App\Enums\ScheduledUpdateStatus;
use App\Models\ScheduledPriceUpdate;
use App\Observers\HotelObserver;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExecuteScheduledPriceUpdates extends Command
{
    protected $signature = 'pricing:execute-scheduled';

    protected $description = 'Execute scheduled price updates that are due';

    public function handle(): int
    {
        $pending = ScheduledPriceUpdate::where('status', ScheduledUpdateStatus::Pending)
            ->where('scheduled_at', '<=', Carbon::now())
            ->with(['roomType', 'hotel'])
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No scheduled price updates to execute.');

            return self::SUCCESS;
        }

        $executed = 0;
        $failed = 0;

        foreach ($pending as $update) {
            try {
                $this->executeUpdate($update);
                $update->update([
                    'status' => ScheduledUpdateStatus::Executed,
                    'executed_at' => Carbon::now(),
                ]);
                $executed++;
            } catch (\Throwable $e) {
                $update->update(['status' => ScheduledUpdateStatus::Failed]);
                $this->error("Failed to execute update #{$update->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Executed: {$executed}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function executeUpdate(ScheduledPriceUpdate $update): void
    {
        $field = $update->field;
        $newValue = $update->new_value;

        if ($update->room_type_id && $update->roomType) {
            $roomType = $update->roomType;
            $roomType->{$field} = $newValue;
            $roomType->save();

            // Recalculate hotel cached min_price
            if ($field === 'base_price' && $roomType->hotel) {
                HotelObserver::recalculateForHotel($roomType->hotel);
            }
        } elseif ($update->hotel_id && $update->hotel) {
            $hotel = $update->hotel;
            $hotel->{$field} = $newValue;
            $hotel->save();
        }
    }
}
