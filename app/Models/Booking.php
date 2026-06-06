<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_no',
        'user_id',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_country',
        'special_requests',
        'check_in',
        'check_out',
        'nights',
        'adults',
        'children',
        'subtotal',
        'discount',
        'tax',
        'total_price',
        'payment_method',
        'payment_proof',
        'status',
    ];

    /**
     * Get the user that owns the booking.
     *
     * @return BelongsTo<User, Booking>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room associated with the booking.
     *
     * @return BelongsTo<Room, Booking>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
