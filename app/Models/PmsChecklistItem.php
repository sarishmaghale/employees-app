<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'item_title',
        'completed'
    ];

    public function checklist()
    {
        return $this->belongsTo(PmsChecklist::class);
    }
}
