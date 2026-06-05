<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['image', 'caption', 'order_index'])]
class Gallery extends Model
{
    //
}
