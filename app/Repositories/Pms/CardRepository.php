<?php

namespace App\Repositories\Pms;

use App\Models\Pms\PmsCard;
use Illuminate\Support\Facades\DB;

class CardRepository
{
    public function create(array $data): PmsCard
    {
        $board_id = $data['board_id'];
        $latestPosition = PmsCard::where('board_id', $board_id)->max('position');
        $position = $latestPosition ? $latestPosition + 1 : 1;
        $card = PmsCard::create([
            'title' => $data['title'],
            'board_id' => $board_id,
            'position' => $position
        ]);
        return $card;
    }

    public function remove(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $card = PmsCard::with([
                'tasks.comments',
                'tasks.checklists',
                'tasks.labels',
                'tasks.files',
                'tasks'
            ])->find($id);
            if (!$card) return false;
            foreach ($card->tasks as $task) {
                $task->comments()->delete();
                foreach ($task->checklists as $checklist) {
                    $checklist->items()->delete();
                    $checklist->delete();
                }
                foreach ($task->files as $file) {
                    $file->delete();
                }
                $task->labels()->detach();
                $task->delete();
            }
            $card->delete();
            return true;
        });
    }
}
