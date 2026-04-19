@extends('emails.layouts.base', [
    'domainName' => $domain->name ?? 'Dubai Apartments',
    'domainEmail' => $domain->email ?? '',
    'domainPhone' => $domain->phone ?? '',
    'domainAddress' => $domain->address ?? '',
])

@section('subject', 'New Property: ' . ($hotel->name ?? 'Hotel') . ' - ' . ($domain->name ?? 'Dubai Apartments'))

@section('preheader', 'Discover ' . ($hotel->name ?? 'our new hotel') . ' - now available on ' . ($domain->name ?? 'Dubai Apartments') . '!')

@section('content')
    {{-- Announcement header --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="background-color: #fef3c7; padding: 6px 16px; border-radius: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: 600; color: #92400e; text-align: center;">
                            New Property Added!
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Hotel name --}}
    <h2 style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 26px; font-weight: 700; line-height: 1.3; color: #1a1d29; text-align: center;">
        {{ $hotel->name ?? 'New Hotel' }}
    </h2>

    {{-- Star rating --}}
    @if($hotel->star_rating ?? false)
        <p style="margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 18px; color: #f59e0b; text-align: center; letter-spacing: 2px;">
            {{ str_repeat('&#9733;', (int) $hotel->star_rating) }}{{ str_repeat('&#9734;', max(0, 5 - (int) $hotel->star_rating)) }}
        </p>
    @endif

    {{-- Location --}}
    @if($hotel->location?->name || $hotel->location?->city)
        <p style="margin: 0 0 20px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #6b7280; text-align: center;">
            {{ $hotel->location?->name ?? '' }}{{ ($hotel->location?->name && $hotel->location?->city) ? ', ' : '' }}{{ $hotel->location?->city ?? '' }}
        </p>
    @endif

    {{-- Description --}}
    <p style="margin: 0 0 28px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7; color: #4b5563;">
        {{ $hotel->short_description ?? Str::limit(strip_tags($hotel->description ?? ''), 200, '...') }}
    </p>

    {{-- Key details grid (2 columns) --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background-color: #f9fafb; padding: 16px 20px; width: 50%; border-bottom: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; font-family: Arial, Helvetica, sans-serif; vertical-align: top;">
                <p style="margin: 0 0 2px 0; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Starting From</p>
                <p style="margin: 0; font-size: 18px; font-weight: 700; color: #667eea;">AED {{ number_format($hotel->min_price ?? 0, 2) }}<span style="font-size: 13px; font-weight: 400; color: #6b7280;">/night</span></p>
            </td>
            <td style="background-color: #f9fafb; padding: 16px 20px; width: 50%; border-bottom: 1px solid #e5e7eb; font-family: Arial, Helvetica, sans-serif; vertical-align: top;">
                <p style="margin: 0 0 2px 0; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Location</p>
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #1a1d29;">{{ $hotel->location?->name ?? 'N/A' }}</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f9fafb; padding: 16px 20px; width: 50%; border-right: 1px solid #e5e7eb; font-family: Arial, Helvetica, sans-serif; vertical-align: top;">
                <p style="margin: 0 0 2px 0; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Beach Access</p>
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: {{ ($hotel->is_beach_access ?? false) ? '#059669' : '#6b7280' }};">
                    {{ ($hotel->is_beach_access ?? false) ? 'Yes' : 'No' }}
                </p>
            </td>
            <td style="background-color: #f9fafb; padding: 16px 20px; width: 50%; font-family: Arial, Helvetica, sans-serif; vertical-align: top;">
                <p style="margin: 0 0 2px 0; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Family Friendly</p>
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: {{ ($hotel->is_family_friendly ?? false) ? '#059669' : '#6b7280' }};">
                    {{ ($hotel->is_family_friendly ?? false) ? 'Yes' : 'No' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Amenities --}}
    @if($hotel->amenities && $hotel->amenities->count() > 0)
        <h3 style="margin: 0 0 12px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; color: #1a1d29; text-transform: uppercase; letter-spacing: 0.5px;">
            Amenities
        </h3>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
            <tr>
                <td>
                    @foreach($hotel->amenities->take(6) as $amenity)
                        <span style="display: inline-block; background-color: #eef2ff; color: #4338ca; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 500; padding: 5px 12px; border-radius: 20px; margin: 0 4px 6px 0;">
                            {{ $amenity->name ?? '' }}
                        </span>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

    {{-- CTA button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="border-radius: 6px; background-color: #667eea;">
                            <a href="#explore-hotel" style="display: inline-block; padding: 14px 32px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px; border: 1px solid #667eea;">
                                Explore This Hotel
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Divider --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 16px;">
        <tr>
            <td style="border-top: 1px solid #e5e7eb; font-size: 1px; line-height: 1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Footer note --}}
    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; color: #9ca3af; text-align: center;">
        You are receiving this because you subscribed to the {{ $domain->name ?? 'Dubai Apartments' }} newsletter.
    </p>
@endsection
