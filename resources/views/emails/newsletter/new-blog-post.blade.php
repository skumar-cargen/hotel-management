@extends('emails.layouts.base', [
    'domainName' => $domain->name ?? 'Dubai Apartments',
    'domainEmail' => $domain->email ?? '',
    'domainPhone' => $domain->phone ?? '',
    'domainAddress' => $domain->address ?? '',
])

@section('subject', 'New from ' . ($domain->name ?? 'Dubai Apartments') . ': ' . ($blogPost->title ?? ''))

@section('preheader', ($blogPost->excerpt ?? Str::limit(strip_tags($blogPost->content ?? ''), 120, '...')))

@section('content')
    {{-- Subject line area --}}
    <p style="margin: 0 0 4px 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; font-weight: 600; color: #667eea; text-transform: uppercase; letter-spacing: 1px;">
        New from {{ $domain->name ?? 'Dubai Apartments' }}
    </p>

    {{-- Featured image --}}
    @if(!empty($blogPost->featured_image))
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px; margin-top: 16px;">
            <tr>
                <td>
                    <img src="{{ $blogPost->featured_image }}" alt="{{ $blogPost->title ?? 'Featured image' }}" width="520" style="display: block; width: 100%; max-width: 520px; height: auto; border-radius: 8px; border: 0;">
                </td>
            </tr>
        </table>
    @endif

    {{-- Category badge --}}
    @if($blogPost->category?->name)
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 12px;">
            <tr>
                <td style="background-color: #eef2ff; padding: 4px 12px; border-radius: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 600; color: #667eea;">
                    {{ $blogPost->category->name }}
                </td>
            </tr>
        </table>
    @endif

    {{-- Title --}}
    <h2 style="margin: 0 0 16px 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: 700; line-height: 1.3; color: #1a1d29;">
        {{ $blogPost->title ?? 'Untitled Post' }}
    </h2>

    {{-- Excerpt --}}
    <p style="margin: 0 0 24px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7; color: #4b5563;">
        {{ $blogPost->excerpt ?? Str::limit(strip_tags($blogPost->content ?? ''), 200, '...') }}
    </p>

    {{-- Read More button --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 28px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="border-radius: 6px; background-color: #667eea;">
                            <a href="#read-more" style="display: inline-block; padding: 14px 32px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 6px; border: 1px solid #667eea;">
                                Read More
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Tags --}}
    @if(!empty($blogPost->tags) && is_array($blogPost->tags) && count($blogPost->tags) > 0)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 24px;">
            <tr>
                <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #6b7280; padding-bottom: 8px;">
                    Tags:
                </td>
            </tr>
            <tr>
                <td>
                    @foreach($blogPost->tags as $tag)
                        {{-- Each tag as a pill badge --}}
                        <span style="display: inline-block; background-color: #f3f4f6; color: #4b5563; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 500; padding: 4px 10px; border-radius: 20px; margin: 0 4px 6px 0;">
                            {{ $tag }}
                        </span>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

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
