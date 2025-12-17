<?php

namespace App\Models;

use App\Traits\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PaymentMethod extends Model
{
    use HasFactory;
    use Blamable;

    protected $table = 'payment_methods';
    protected $fillable = [
        'name',
        'description'
    ];

    public function details()
    {
        return $this->hasMany(PaymentMethodDetail::class);
    }

    public function addGroupId($groupIds)
    {
        $group_ids = collect($groupIds);
        foreach ($group_ids as $group_id) {
            $this->details()->create([
                'group_id'=> $group_id
            ]);
        }
    }
    public function updateGroupId($groupIds)
    {
        /** delete all detail */
        $this->details()->delete();

        /** create detail */
        $group_ids = collect($groupIds);
        foreach ($group_ids as $group_id) {
            PaymentMethodDetail::create([
                'payment_method_id' => $this->id,
                'group_id' => $group_id
            ]);
        }
    }
}
