<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
      'purchase_id',
      'rater_id',
      'ratee_id',
      'rating',
    ];

    public function purchase()
    {
      return $this->belongsTo(Purchase::class);
    }

    public function rater()
    {
      return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee()
    {
      return $this->belongsTo(User::class, 'ratee_id');
    }
}
