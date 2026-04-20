<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>@yield('subject', $domainName ?? 'Abu Dhabi Hotels')</title>
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
<body style="margin: 0; padding: 0; width: 100%; word-spacing: normal; background-color: #F0EDE8; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    {{-- Preheader text (hidden, shown in inbox preview) --}}
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
        @yield('preheader', '')
        &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
    </div>

    {{-- Wrapper --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F0EDE8;">
        <tr>
            <td align="center" style="padding: 32px 16px;">

                {{-- Main container --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; margin: 0 auto;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #0F1B2D; padding: 32px 40px 28px 40px; text-align: center;">
                            <h1 style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 24px; font-weight: 700; color: #ffffff; letter-spacing: 1px;">
                                {{ $domainName ?? 'Abu Dhabi Hotels' }}
                            </h1>
                            <p style="margin: 6px 0 0 0; font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #C8A97E; text-transform: uppercase; letter-spacing: 3px; font-weight: 600;">
                                Premium Hotel Booking
                            </p>
                        </td>
                    </tr>

                    {{-- Gold accent line --}}
                    <tr>
                        <td style="height: 3px; background-color: #C8A97E; font-size: 1px; line-height: 1px;">&nbsp;</td>
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
                        <td style="background-color: #0F1B2D; padding: 28px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                {{-- Brand --}}
                                <tr>
                                    <td style="text-align: center; padding-bottom: 14px;">
                                        <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; color: #C8A97E; letter-spacing: 1px;">
                                            {{ $domainName ?? 'Abu Dhabi Hotels' }}
                                        </p>
                                    </td>
                                </tr>

                                {{-- Divider --}}
                                <tr>
                                    <td align="center" style="padding-bottom: 14px;">
                                        <table role="presentation" width="50" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="border-top: 1px solid #1E2D42; font-size: 1px; line-height: 1px;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                {{-- Contact --}}
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.8; color: #8899AA; text-align: center;">
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

                                {{-- Copyright --}}
                                <tr>
                                    <td style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.5; color: #556677; text-align: center; padding-top: 16px;">
                                        &copy; {{ date('Y') }} {{ $domainName ?? 'Abu Dhabi Hotels' }}. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
