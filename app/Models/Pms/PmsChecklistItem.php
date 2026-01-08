<?php

namespace App\Models\Pms;

use Illuminate\Database\Eloquent\Model;

class PmsChecklistItem extends Model
{
    protected $fillable = [
        'checklist_id',
        'item_title',
        'isCompleted'
    ];

    public function checklist()
    {
        return $this->belongsTo(PmsChecklist::class);
    }
}
