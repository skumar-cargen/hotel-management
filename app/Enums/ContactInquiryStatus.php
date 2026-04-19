<?php

namespace App\Enums;

enum ContactInquiryStatus: string
{
    case Received = 'new';
    case Read = 'read';
    case Replied = 'replied';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'New',
            self::Read => 'Read',
            self::Replied => 'Replied',
        };
    }
}
