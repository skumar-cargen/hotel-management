@extends('emails.layouts.base')

@section('subject', 'New Customer Registration')
@section('preheader', 'A new customer just signed up' . ($domain?->name ? ' on ' . $domain->name : ''))

@section('content')
    <h2 style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 22px; font-weight: 700; color: #0F1B2D;">
        New Customer Registered
    </h2>

    <p style="margin: 0 0 20px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.6; color: #1a1d29;">
        A new customer has just signed up{{ $domain?->name ? ' on ' . $domain->name : '' }}.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; margin: 0 0 20px 0;">
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677; width: 35%;">
                Name
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-weight: 600;">
                {{ trim($customer->first_name . ' ' . $customer->last_name) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Email
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                <a href="mailto:{{ $customer->email }}" style="color: #0F1B2D; text-decoration: none;">{{ $customer->email }}</a>
            </td>
        </tr>
        @if($customer->phone)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Phone
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                {{ $customer->phone }}
            </td>
        </tr>
        @endif
        @if($customer->nationality)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Nationality
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                {{ $customer->nationality }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Sign-up Method
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; text-transform: capitalize;">
                {{ $registrationMethod }}
            </td>
        </tr>
        @if($domain)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Domain
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                {{ $domain->name }} <span style="color: #8899AA; font-size: 12px;">({{ $domain->slug }})</span>
            </td>
        </tr>
        @endif
        @if($ipAddress)
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                IP Address
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; border-bottom: 1px solid #E8E2D5; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29; font-family: monospace;">
                {{ $ipAddress }}
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 10px 12px; background-color: #F7F4EE; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #556677;">
                Registered At
            </td>
            <td style="padding: 10px 12px; background-color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #1a1d29;">
                {{ $customer->created_at?->format('d M Y, H:i') }}
            </td>
        </tr>
    </table>

    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; color: #556677;">
        This is an automated notification from the booking system.
    </p>
@endsection
