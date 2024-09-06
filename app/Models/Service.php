<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
