<?php

namespace App\Enums;

enum CareerApplicationStatus: string
{
    case Received = 'new';
    case Reviewed = 'reviewed';
    case Shortlisted = 'shortlisted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'New',
            self::Reviewed => 'Reviewed',
            self::Shortlisted => 'Shortlisted',
            self::Rejected => 'Rejected',
        };
    }
}
