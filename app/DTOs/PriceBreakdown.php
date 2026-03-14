<?php

namespace App\DTOs;

class PriceBreakdown
{
    public function __construct(
        public readonly float $basePrice,
        public readonly array $adjustments,
        public readonly float $finalPerNight,
        public readonly array $nightlyBreakdown,
        public readonly int $numNights,
        public readonly int $numRooms,
        public readonly float $subtotal,
        public readonly float $taxPercentage,
        public readonly float $taxAmount,
        public readonly float $tourismFee,
        public readonly float $serviceCharge,
        public readonly float $totalAmount,
        public readonly string $currency = 'AED',
    ) {}

    public static function make(array $data): self
    {
        return new self(
            basePrice: $data['basePrice'],
            adjustments: $data['adjustments'] ?? [],
            finalPerNight: $data['finalPerNight'],
            nightlyBreakdown: $data['nightlyBreakdown'] ?? [],
            numNights: $data['numNights'],
            numRooms: $data['numRooms'],
            subtotal: $data['subtotal'],
            taxPercentage: $data['taxPercentage'],
            taxAmount: $data['taxAmount'],
            tourismFee: $data['tourismFee'],
            serviceCharge: $data['serviceCharge'] ?? 0,
            totalAmount: $data['totalAmount'],
        );
    }

    public function toArray(): array
    {
        return [
            'base_price' => $this->basePrice,
            'adjustments' => $this->adjustments,
            'final_per_night' => $this->finalPerNight,
            'nightly_breakdown' => $this->nightlyBreakdown,
            'num_nights' => $this->numNights,
            'num_rooms' => $this->numRooms,
            'subtotal' => $this->subtotal,
            'tax_percentage' => $this->taxPercentage,
            'tax_amount' => $this->taxAmount,
            'tourism_fee' => $this->tourismFee,
            'service_charge' => $this->serviceCharge,
            'total_amount' => $this->totalAmount,
            'currency' => 'AED',
        ];
    }
}
