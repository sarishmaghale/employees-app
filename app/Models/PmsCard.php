<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsCard extends Model
{
    protected $table = 'pms_cards';
    protected $fillable = [
        'title',
        'board_id',
        'position'
    ];

    public function board()
    {
        return $this->belongsTo(PmsBoard::class, 'board_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(PmsTask::class, 'card_id', 'id');
    }
}
