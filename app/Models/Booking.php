<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'parent_id',
        'invoice_no',
        'user_id',
        'room_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_country',
        'special_requests',
        'include_breakfast',
        'include_extra_bed',
        'late_checkout',
        'check_in',
        'check_out',
        'nights',
        'guests',
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

    /**
     * Get the parent booking.
     *
     * @return BelongsTo<Booking, Booking>
     */
    public function parentBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'parent_id');
    }

    /**
     * Get the child bookings.
     *
     * @return HasMany<Booking>
     */
    public function childBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'parent_id');
    }
}
