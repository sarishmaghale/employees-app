<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Models\PmsLabel;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function labels()
    {
        return view('components.pms-labels');
    }

    public function storeLabel(Request $request)
    {
        $result = PmsLabel::create([
            'title' => $request->title,
            'color' => $request->color,
        ]);
        if ($result && $result->exists()) return JsonResponse::success(data: $result);
        else return JsonResponse::error(message: 'Failed to add!');
    }

    public function deleteLabel(int $id)
    {
        $label = PmsLabel::find($id);
        if ($label) {
            $label->tasks()->detach();
            $label->delete();
            return JsonResponse::success(message: 'Deleted successfully');
        }
        return JsonResponse::error(message: 'Failed to delete');
    }

    public function updateLabel(Request $request, int $id)
    {
        $label = PmsLabel::find($id);
        if ($label) {
            $label->update([
                'title' => $request->title,
                'color' => $request->color
            ]);
            return JsonResponse::success(message: 'Updated!');
        }
        return JsonResponse::error(message: 'Failed to edit!');
    }
}
