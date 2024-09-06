<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Organisation extends Model
{
    use HasFactory;
    use UsesTenantConnection;
    use Billable;

    protected $table = 'organisation';

    protected $fillable = [
        'name', 'billing_email', 'billing_info', 'require2FA',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function (Organisation $model) {
            $model->createAsStripeCustomer([
                'name' => $model->name,
            ]);
        });

        static::creating(function (Organisation $organisation) {
            $organisation->trial_ends_at = now()->addDays(30);
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
