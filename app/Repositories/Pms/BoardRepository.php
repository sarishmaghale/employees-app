<?php

namespace App\Repositories\Pms;

use App\Models\Pms\PmsBoard;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class BoardRepository
{
    public function getCreatedBoardList(): Collection
    {
        return PmsBoard::with('members')
            ->where('created_by', Auth::id())
            ->select('id', 'board_name', 'image')
            ->get();
    }

    public function getAssociatedBoardList(): Collection
    {
        $boards = PmsBoard::with('members', 'creator')
            ->whereRelation('members', 'employee_id', Auth::id())
            ->where('created_by', '!=', Auth::id())
            ->select('id', 'board_name', 'created_by', 'image')
            ->get();
        return $boards;
    }

    public function getBoardDetails(int $boardId): ?PmsBoard
    {
        return PmsBoard::with([
            'members',
            'cards.tasks.checklists.items',
            'cards.tasks' => function ($query) {
                $query->with([
                    'assignedEmployees:id,username',
                    'assignedEmployees.detail:id,employee_id,profile_image'
                ])->withCount([
                    'checklistItems as total_items',
                    'checklistItems as completed_items' => fn($q) => $q->where('isCompleted', true)
                ]);
            }
        ])->find($boardId);
    }

    public function create(string $boardName): PmsBoard
    {
        return DB::transaction(function () use ($boardName) {
            $board = PmsBoard::create([
                'board_name' => $boardName,
                'created_by' => Auth::id(),
            ]);
            $board->members()->attach(Auth::id());
            return $board;
        });
    }

    public function addMember(int $boardId, int $employeeId): array
    {
        $board = PmsBoard::find($boardId);
        if ($board->members()->where('employee_id', $employeeId)->exists()) {
            return [
                'success' => false,
                'message' => 'This member is already part of this board.'
            ];
        }

        $board->members()->attach($employeeId);
        $member = $board->members()
            ->where('employee_id', $employeeId)
            ->first();
        return [
            'success' => true,
            'message' => 'Member added successfully.',
            'board' => $member
        ];
    }

    public function updateCoverImage(int $boardId, UploadedFile $file): ?PmsBoard
    {
        $board = PmsBoard::find($boardId);
        if (!$board) return null;

        if ($board->image && Storage::disk('public')->exists($board->image)) {
            Storage::disk('public')->delete($board->image);
        }

        $filePath = $file->store('boards', 'public');

        $board->update(['image' => $filePath]);

        return $board;
    }
}
