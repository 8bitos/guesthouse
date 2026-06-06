<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type', 'price', 'capacity', 'description', 'status', 'image', 'allow_breakfast', 'allow_extra_bed', 'allow_late_checkout'])]
class Room extends Model
{
    use HasFactory;
}
