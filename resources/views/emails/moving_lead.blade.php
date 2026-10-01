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

                @if(!is_null($quote->estimate_total))
                {{-- Estimate (internal use only, never shown to the customer) --}}
                <tr>
                    <td style="padding:0 24px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse; background-color:#fffbeb; border:1px solid #fde68a; border-radius:8px;">
                            <tr>
                                <td style="padding:16px;">
                                    <p style="margin:0 0 4px; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:.04em; color:#92400e;">
                                        Internal estimate — do not share as-is
                                    </p>
                                    <p style="margin:0 0 8px; font-size:24px; font-weight:bold; color:#1b1b1b;">
                                        ${{ number_format($quote->estimate_range_low, 0) }} – ${{ number_format($quote->estimate_range_high, 0) }}
                                    </p>
                                    <p style="margin:0; font-size:13px; color:#555;">
                                        {{ $quote->estimate_hours }} hours @
                                        ${{ $quote->estimate_breakdown['rate'] ?? '—' }}/hr
                                        (target {{ $quote->estimate_breakdown['range_pct'] ?? '—' }}% range)
                                        @if(!is_null($quote->estimate_breakdown['miles'] ?? null))
                                            &middot; {{ $quote->estimate_breakdown['miles'] }} miles (straight-line, origin ZIP to destination ZIP)
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

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
                                <td style="padding:6px 0; font-weight:bold;">Phone:</td>
                                <td style="padding:6px 0;">
                                    {{ $quote->phone ?? '—' }}
                                    @if($quote->sms_consent) (OK to text) @endif
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Email:</td>
                                <td style="padding:6px 0;">{{ $quote->email ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Origin ZIP:</td>
                                <td style="padding:6px 0;">{{ $quote->origin_zip ?? $quote->origin_address ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Destination ZIP:</td>
                                <td style="padding:6px 0;">{{ $quote->destination_zip ?? $quote->destination_address ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Preferred Date:</td>
                                <td style="padding:6px 0;">
                                    @if($quote->date_flexible)
                                        Flexible
                                    @else
                                        {{ $quote->preferred_date
                                            ? \Carbon\Carbon::parse($quote->preferred_date)->format('F j, Y')
                                            : '—'
                                        }}
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Schedule:</td>
                                <td style="padding:6px 0;">{{ $quote->schedule ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Move Type:</td>
                                <td style="padding:6px 0;">{{ $quote->move_type ?? '—' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Bedrooms:</td>
                                <td style="padding:6px 0;">{{ $quote->bedrooms ?? '—' }}</td>
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
                                <td style="padding:6px 0;">{{ ($quote->packing_service ?? false) ? 'Yes' : 'No' }}</td>
                            </tr>

                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Special Items:</td>
                                <td style="padding:6px 0;">
                                    {{ !empty($quote->special_items) ? implode(', ', $quote->special_items) : 'None' }}
                                </td>
                            </tr>

                            @if(!empty($quote->photos))
                            <tr>
                                <td style="padding:6px 0; font-weight:bold;">Photos:</td>
                                <td style="padding:6px 0;">{{ count($quote->photos) }} attached</td>
                            </tr>
                            @endif

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

                @if(!is_null($quote->estimate_total))
                {{-- Estimate breakdown --}}
                <tr>
                    <td style="padding:0 24px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse; font-size:14px; color:#333;">
                            <tr>
                                <td colspan="2" style="padding:14px 0 4px; font-weight:bold; border-top:1px solid #eee;">
                                    Estimate Breakdown
                                </td>
                            </tr>
                            @foreach($quote->estimate_breakdown['breakdown'] ?? [] as $line)
                            <tr>
                                <td style="padding:4px 0; color:#555;">{{ $line['label'] }}</td>
                                <td style="padding:4px 0; text-align:right;">${{ number_format($line['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="padding:8px 0; font-weight:bold; border-top:1px solid #eee;">Total</td>
                                <td style="padding:8px 0; font-weight:bold; text-align:right; border-top:1px solid #eee;">
                                    ${{ number_format($quote->estimate_total, 2) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                @if(!empty($unconfirmedLabels ?? []))
                <tr>
                    <td style="padding:0 24px 20px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="border-collapse:collapse; background-color:#fef2f2; border:1px solid #fecaca; border-radius:8px;">
                            <tr>
                                <td style="padding:14px 16px; font-size:13px; color:#991b1b;">
                                    This estimate uses {{ count($unconfirmedLabels) }} value{{ count($unconfirmedLabels) === 1 ? '' : 's' }} you haven't confirmed yet:
                                    <strong>{{ implode(', ', $unconfirmedLabels) }}</strong>.
                                    Review it before giving it to the customer.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding:0 24px 24px;">
                        <a href="{{ route('dashboard.leads') }}#quote-{{ $quote->id }}"
                           style="display:inline-block; background-color:#1b1b1b; color:#ffffff; text-decoration:none; padding:10px 18px; border-radius:6px; font-size:13px; font-weight:bold;">
                            View this lead in the admin dashboard
                        </a>
                    </td>
                </tr>
                @endif

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
