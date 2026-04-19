<?php

namespace App\Enums;

enum ScheduledUpdateStatus: string
{
    case Pending = 'pending';
    case Executed = 'executed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Executed => 'Executed',
            self::Failed => 'Failed',
        };
    }
}
