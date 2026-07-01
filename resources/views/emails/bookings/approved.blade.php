<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
            box-sizing: border-box;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #0f172a;
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            color: #ca8a04; /* amber-700 */
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 800;
        }
        .header p {
            color: #94a3b8;
            margin: 6px 0 0 0;
            font-size: 12px;
            letter-spacing: 0.05em;
        }
        .content {
            padding: 32px 24px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .intro {
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            overflow: hidden;
        }
        .details-table td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .label {
            color: #64748b;
            font-weight: 600;
            width: 35%;
        }
        .val {
            color: #0f172a;
            font-weight: 700;
        }
        .price-row {
            background-color: #fffbeb;
            border-top: 2px solid #fde68a;
        }
        .price-label {
            color: #92400e;
            font-weight: 800;
        }
        .price-val {
            color: #b45309;
            font-size: 15px;
            font-weight: 900;
        }
        .badge {
            background-color: #dcfce7;
            color: #15803d;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .btn-wrapper {
            text-align: center;
            margin-top: 28px;
            margin-bottom: 12px;
        }
        .btn {
            background-color: #b45309;
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            font-weight: 700;
            font-size: 12px;
            border-radius: 8px;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(180, 83, 9, 0.15);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>Bagus Guest House</h1>
                <p>Kuta, Bali &bull; Reservation Confirmed</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Hi {{ $booking->guest_name }},</p>
                <p class="intro">Great news! We have verified your payment and confirmed your reservation at Bagus Guest House. Below are your booking details:</p>

                <!-- Details Table -->
                <table class="details-table">
                    <tr>
                        <td class="label">Invoice No.</td>
                        <td class="val" style="font-family: monospace; font-size: 13px; color: #b45309;">{{ $booking->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="label">Room</td>
                        <td class="val">
                            @php
                                $roomsList = collect([$booking->room ? $booking->room->name . ' (' . $booking->room->type . ')' : 'Accommodation']);
                                foreach($booking->childBookings as $child) {
                                    if ($child->room) {
                                        $roomsList->push($child->room->name . ' (' . $child->room->type . ')');
                                    }
                                }
                                echo $roomsList->join(', ');
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Stay Dates</td>
                        <td class="val">{{ date('d M Y', strtotime($booking->check_in)) }} - {{ date('d M Y', strtotime($booking->check_out)) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nights</td>
                        <td class="val">{{ $booking->nights }} night(s)</td>
                    </tr>
                    <tr>
                        <td class="label">Guests</td>
                        <td class="val">{{ $booking->guests + $booking->childBookings->sum('guests') }} Guest(s)</td>
                    </tr>
                    @php
                        $addons = [];
                        if ($booking->addons && is_array($booking->addons) && count($booking->addons) > 0) {
                            foreach ($booking->addons as $addonName) {
                                $addons[] = $addonName . ' (' . ($booking->room ? $booking->room->name : 'Room 1') . ')';
                            }
                        } else {
                            if($booking->include_breakfast) $addons[] = 'Breakfast (' . ($booking->room ? $booking->room->name : 'Room 1') . ')';
                            if($booking->include_extra_bed) $addons[] = 'Extra Bed (' . ($booking->room ? $booking->room->name : 'Room 1') . ')';
                            if($booking->late_checkout) $addons[] = 'Late Out (' . ($booking->room ? $booking->room->name : 'Room 1') . ')';
                        }
                        
                        foreach($booking->childBookings as $child) {
                            if ($child->addons && is_array($child->addons) && count($child->addons) > 0) {
                                foreach ($child->addons as $addonName) {
                                    $addons[] = $addonName . ' (' . ($child->room ? $child->room->name : 'Room') . ')';
                                }
                            } else {
                                if($child->include_breakfast) $addons[] = 'Breakfast (' . ($child->room ? $child->room->name : 'Room') . ')';
                                if($child->include_extra_bed) $addons[] = 'Extra Bed (' . ($child->room ? $child->room->name : 'Room') . ')';
                                if($child->late_checkout) $addons[] = 'Late Out (' . ($child->room ? $child->room->name : 'Room') . ')';
                            }
                        }
                    @endphp
                    @if(count($addons) > 0)
                    <tr>
                        <td class="label">Add-ons</td>
                        <td class="val">
                            <span style="font-size: 12px; color: #475569; font-weight: normal;">
                                {{ implode(', ', $addons) }}
                            </span>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Status</td>
                        <td class="val"><span class="badge">Confirmed & Secured</span></td>
                    </tr>
                    <tr class="price-row">
                        <td class="label price-label">Total Amount Paid</td>
                        <td class="val price-val">RP {{ number_format($booking->total_price + $booking->childBookings->sum('total_price'), 0, ',', '.') }}</td>
                    </tr>
                </table>

                <p class="intro" style="margin-top: 20px;">
                    <strong>Check-in Time:</strong> 14:00 - 22:00<br>
                    <strong>Check-out Time:</strong> 08:00 - 12:00
                </p>

                <p class="intro">If you have any questions or would like to request custom activities (such as jeep tours, hiking, or dining reservations), please chat with our customer support on WhatsApp.</p>

                <div class="btn-wrapper">
                    <a href="https://wa.me/6282169911168" class="btn" style="color: #ffffff !important;">Chat with Us on WhatsApp</a>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} Bagus Guest House. All rights reserved.</p>
                <p>Jl. Majapahit Gg. Muria, Kuta, Bali, Indonesia</p>
            </div>
        </div>
    </div>
</body>
</html>
