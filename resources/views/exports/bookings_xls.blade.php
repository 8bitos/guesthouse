<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
 {!! '<' . 'x:ExcelWorkbook>' !!}
  {!! '<' . 'x:ExcelWorksheets>' !!}
   {!! '<' . 'x:ExcelWorksheet>' !!}
    {!! '<' . 'x:Name>Reservations Report</' . 'x:Name>' !!}
    {!! '<' . 'x:WorksheetOptions>' !!}
     {!! '<' . 'x:DisplayGridlines/>' !!}
    {!! '</' . 'x:WorksheetOptions>' !!}
   {!! '</' . 'x:ExcelWorksheet>' !!}
  {!! '</' . 'x:ExcelWorksheets>' !!}
 {!! '</' . 'x:ExcelWorkbook>' !!}
</xml>
<![endif]-->
<style>
  table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
    font-size: 11px;
  }
  th {
    background-color: #d97706;
    color: #ffffff;
    font-weight: bold;
    border: 1px solid #b5babf;
    padding: 6px;
    text-align: left;
  }
  td {
    border: 1px solid #e2e8f0;
    padding: 6px;
    text-align: left;
  }
  .text-right {
    text-align: right;
  }
  .text-center {
    text-align: center;
  }
  .font-bold {
    font-weight: bold;
  }
  /* Force cell formatting in Excel */
  .text-cell {
    mso-number-format: "\@";
  }
  .currency-cell {
    mso-number-format: "\#\,\#\#0";
  }
  .date-cell {
    mso-number-format: "yyyy\-mm\-dd";
  }
  .datetime-cell {
    mso-number-format: "yyyy\-mm\-dd\ hh\:mm\:ss";
  }
</style>
</head>
<body>
  <h2 style="font-family: Arial, sans-serif; color: #1e293b; margin-bottom: 2px;">Bagus Guest House - Reservations Report</h2>
  <p style="font-family: Arial, sans-serif; font-size: 12px; color: #64748b; margin-top: 0; margin-bottom: 15px;">Generated on: {{ date('Y-m-d H:i:s') }}</p>

  <table>
    <thead>
      <tr>
        <th>Invoice No</th>
        <th>Guest Name</th>
        <th>Guest Email</th>
        <th>Guest Phone</th>
        <th>Guest Country</th>
        <th>Room Name</th>
        <th>Check-In Date</th>
        <th>Check-Out Date</th>
        <th class="text-center">Nights</th>
        <th class="text-center">Guests</th>
        <th class="text-center">Breakfast</th>
        <th class="text-center">Extra Bed</th>
        <th class="text-center">Late Check-out</th>
        <th>Other Add-ons</th>
        <th class="text-right">Subtotal (RP)</th>
        <th class="text-right">Discount (RP)</th>
        <th class="text-right">Tax (RP)</th>
        <th class="text-right">Total Price (RP)</th>
        <th>Status</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($bookings as $booking)
        <tr>
          <td class="text-cell">{{ $booking->invoice_no }}</td>
          <td>{{ $booking->guest_name }}</td>
          <td>{{ $booking->guest_email }}</td>
          <td class="text-cell">{{ $booking->guest_phone }}</td>
          <td>{{ $booking->guest_country }}</td>
          <td>{{ $booking->room ? $booking->room->name : 'Deleted Room' }}</td>
          <td class="date-cell text-center">{{ $booking->check_in }}</td>
          <td class="date-cell text-center">{{ $booking->check_out }}</td>
          <td class="text-center">{{ $booking->nights }}</td>
          <td class="text-center">{{ $booking->guests }}</td>
          <td class="text-center">{{ $booking->include_breakfast ? 'Yes' : 'No' }}</td>
          <td class="text-center">{{ $booking->include_extra_bed ? 'Yes' : 'No' }}</td>
          <td class="text-center">{{ $booking->late_checkout ? 'Yes' : 'No' }}</td>
          <td>
            @php
              $otherAddons = [];
              if ($booking->addons && is_array($booking->addons)) {
                  foreach ($booking->addons as $addonName) {
                      $lowerName = strtolower($addonName);
                      if (strpos($lowerName, 'breakfast') === false && 
                          strpos($lowerName, 'sarapan') === false && 
                          strpos($lowerName, 'extra bed') === false && 
                          strpos($lowerName, 'kasur') === false && 
                          strpos($lowerName, 'late check') === false && 
                          strpos($lowerName, 'late out') === false) {
                          $otherAddons[] = $addonName;
                      }
                  }
              }
            @endphp
            {{ count($otherAddons) > 0 ? implode(', ', $otherAddons) : '-' }}
          </td>
          <td class="currency-cell text-right">{{ $booking->subtotal }}</td>
          <td class="currency-cell text-right">{{ $booking->discount }}</td>
          <td class="currency-cell text-right">{{ $booking->tax }}</td>
          <td class="currency-cell text-right">{{ $booking->total_price }}</td>
          <td>{{ ucfirst($booking->status) }}</td>
          <td class="datetime-cell">{{ $booking->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="20" style="text-align: center; color: #64748b; font-style: italic; padding: 12px;">No records match the selected filters.</td>
        </tr>
      @endforelse
    </tbody>
    @if ($bookings->count() > 0)
      <tfoot>
        <tr style="font-weight: bold; background-color: #f1f5f9;">
          <td colspan="8" style="text-align: right; font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">Total Summary</td>
          <td style="text-align: center; font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('nights') }}</td>
          <td style="text-align: center; font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('guests') }}</td>
          <td colspan="4" style="background-color: #e2e8f0; border-top: 2px solid #b5babf;"></td>
          <td class="currency-cell text-right" style="font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('subtotal') }}</td>
          <td class="currency-cell text-right" style="font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('discount') }}</td>
          <td class="currency-cell text-right" style="font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('tax') }}</td>
          <td class="currency-cell text-right" style="font-weight: bold; background-color: #e2e8f0; border-top: 2px solid #b5babf;">{{ $bookings->sum('total_price') }}</td>
          <td colspan="2" style="background-color: #e2e8f0; border-top: 2px solid #b5babf;"></td>
        </tr>
      </tfoot>
    @endif
  </table>
</body>
</html>
