<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\StoreBoardRequest;
use App\Repositories\KanbanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    public function __construct(protected KanbanRepository $boardHelper) {}

    public function index()
    {
        // $employee_id = Auth::id();
        // $category_id = 1;
        // $columns = $this->boardHelper->getUserColumns(categoryId: $category_id, employeeId: $employee_id);
        // foreach ($columns as $column) {
        //     dd($column->status->name);
        // }

        return view('kanban-board.index');
    }
    public function show(int $categoryId)
    {
        $employee_id = Auth::id();
        $columns = $this->boardHelper->fetchStatusByCategory(categoryId: $categoryId, employeeId: $employee_id);
        return response()->json([
            'success' => true,
            'data' => $columns
        ]);
    }
    public function storeBoard(StoreBoardRequest $request)
    {
        $data = $request->validated();
        $result = $this->boardHelper->addNewBoard($data);
        if ($result) return JsonResponse::success(message: 'New Board added');
        else return JsonResponse::error(message: 'Failed to add board');
    }
}
