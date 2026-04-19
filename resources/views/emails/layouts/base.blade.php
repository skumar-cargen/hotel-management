<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('subject', $domainName ?? 'Dubai Apartments')</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; width: 100%; word-spacing: normal; background-color: #f4f5f7; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    {{-- Preheader text (hidden, shown in inbox preview) --}}
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        @yield('preheader', '')
        {{-- Pad with whitespace to push other content out of preview --}}
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    {{-- Wrapper table --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f5f7;">
        <tr>
            <td align="center" style="padding: 24px 16px;">

                {{-- Main container --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; margin: 0 auto;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #1a1d3a; padding: 28px 40px; border-radius: 8px 8px 0 0; text-align: center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="text-align: center;">
                                        <h1 style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: 0.5px;">
                                            {{ $domainName ?? 'Dubai Apartments' }}
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px 40px 32px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.6; color: #1a1d29;">
                                        @yield('content')
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 32px 40px; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                {{-- Contact info --}}
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.6; color: #6b7280; text-align: center; padding-bottom: 16px;">
                                        <strong style="color: #1a1d29;">{{ $domainName ?? 'Dubai Apartments' }}</strong><br>
                                        @if(!empty($domainAddress))
                                            {{ $domainAddress }}<br>
                                        @endif
                                        @if(!empty($domainPhone))
                                            Phone: {{ $domainPhone }}<br>
                                        @endif
                                        @if(!empty($domainEmail))
                                            Email: {{ $domainEmail }}
                                        @endif
                                    </td>
                                </tr>

                                {{-- Divider --}}
                                <tr>
                                    <td style="padding-bottom: 16px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-top: 1px solid #e5e7eb; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Unsubscribe and copyright --}}
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.5; color: #9ca3af; text-align: center;">
                                        <a href="#unsubscribe" style="color: #667eea; text-decoration: underline;">Unsubscribe</a>
                                        &nbsp;&bull;&nbsp;
                                        <a href="#preferences" style="color: #667eea; text-decoration: underline;">Email Preferences</a>
                                        <br>
                                        &copy; {{ date('Y') }} {{ $domainName ?? 'Dubai Apartments' }}. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                {{-- End main container --}}

            </td>
        </tr>
    </table>
    {{-- End wrapper --}}
</body>
</html>
