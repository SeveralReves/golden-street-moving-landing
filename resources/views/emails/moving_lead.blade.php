<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Moving Lead</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
@php
    $logo = "cid:logo-1";
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f4; padding:20px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">

                {{-- Header with Logo --}}
                <tr>
                    <td style="padding:20px 0; text-align:center; background-color:#ffffff;">
                        <img src="{{ $logo }}" alt="Golden Street Moving" style="max-width:180px; height:auto;">
                    </td>
                </tr>

                {{-- Header Title --}}
                <tr>
                    <td style="background:linear-gradient(90deg,#f4b200,#f9d25b); padding:20px 24px; color:#1b1b1b;">
                        <h1 style="margin:0; font-size:22px;">New Moving Request</h1>
                        <p style="margin:4px 0 0; font-size:14px;">
                            Golden Street Moving – New contact from website form
                        </p>
                    </td>
                </tr>

                {{-- Intro --}}
                <tr>
                    <td style="padding:20px 24px 0;">
                        <p style="margin:0 0 12px; font-size:14px; color:#555;">
                            You’ve received a new moving quote request with the following details:
                        </p>
                    </td>
                </tr>

                {{-- Main Data --}}
                <tr>
                    <td style="padding:0 24px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse; font-size:14px; color:#333;">

                            <tr>
                                <td width="35%" style="padding:6px 0; font-weight:bold;">Name:</td>
                                <td style="padding:6px 0;">{{ $quote->name ?? '' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Email:</td>
                                <td style="padding:6px 0;">{{ $quote->email ?? '' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Origin Address:</td>
                                <td style="padding:6px 0;">{{ $quote->origin_address ?? '' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Destination Address:</td>
                                <td style="padding:6px 0;">{{ $quote->destination_address ?? '' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Preferred Date:</td>
                                <td style="padding:6px 0;">{{ $quote->preferred_date 
                                    ? \Carbon\Carbon::parse($quote->preferred_date)->format('F j, Y') 
                                    : '—' 
                                }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Schedule:</td>
                                <td style="padding:6px 0;">{{ $quote->schedule ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Move Type:</td>
                                <td style="padding:6px 0;">{{ $quote->move_type ?? '' }}</td>
                            </tr>

                            {{-- ORIGIN --}}
                            <tr>
                                <td colspan="2" style="padding:14px 0 4px; font-weight:bold; border-top:1px solid #eee;">
                                    Origin Details
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Floor:</td>
                                <td style="padding:6px 0;">{{ $quote->origin_floor ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Elevator:</td>
                                <td style="padding:6px 0;">{{ ($quote->origin_elevator ?? false) ? 'Yes' : 'No' }}</td>
                            </tr>

                            {{-- DESTINATION --}}
                            <tr>
                                <td colspan="2" style="padding:14px 0 4px; font-weight:bold; border-top:1px solid #eee;">
                                    Destination Details
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Floor:</td>
                                <td style="padding:6px 0;">{{ $quote->destination_floor ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Elevator:</td>
                                <td style="padding:6px 0;">{{ ($quote->destination_elevator ?? false) ? 'Yes' : 'No' }}</td>
                            </tr>

                            {{-- SERVICES --}}
                            <tr>
                                <td colspan="2" style="padding:14px 0 4px; font-weight:bold; border-top:1px solid #eee;">
                                    Services
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Packing Service:</td>
                                <td style="padding:6px 0;">{{ $quote->packing_service ?? '—' }}</td>
                            </tr>

                            {{-- COMMENTS --}}
                            <tr>
                                <td colspan="2" style="padding:14px 0 4px; font-weight:bold; border-top:1px solid #eee;">
                                    Comments
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="padding:6px 0; line-height:1.5; color:#555;">
                                    {!! nl2br(e($quote->comments ?? 'No additional comments.')) !!}
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:16px 24px 20px; background-color:#f9f9f9; font-size:12px; color:#888; text-align:center;">
                        This email was generated automatically from the Golden Street Moving website form.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
