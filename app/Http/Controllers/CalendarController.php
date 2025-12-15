<?php

namespace App\Http\Controllers;

use App\Helpers\JsonReponse;
use App\Http\Requests\StoreTaskRequest;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct(protected TaskRepository $taskRepo) {}

    public function calendar()
    {
        return view('calendar');
    }

    public function show(Request $request)
    {
        $filters = $request->only(['start', 'end', 'category_id']);
        $userId = Auth::user()->id;
        $result = $this->taskRepo->getAllById($userId, $filters);
        return response()->json($result);
    }
}
