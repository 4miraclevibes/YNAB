<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'name', 'target_amount', 'current_amount', 'deadline'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function goalTransactions()
    {
        return $this->hasMany(GoalTransaction::class);
    }
}
