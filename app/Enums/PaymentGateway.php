<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case Mashreq = 'mashreq';
    case Mpgs = 'mpgs';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Mashreq => 'Mashreq',
            self::Mpgs => 'MPGS',
            self::Manual => 'Manual',
        };
    }
}
