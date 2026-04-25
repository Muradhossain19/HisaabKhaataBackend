<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        $user = $request->user();
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }
        return response()->json(['data' => $query->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string', 'color' => 'nullable|string', 'icon' => 'nullable|string']);
        $user = $request->user();
        if ($user) $data['user_id'] = $user->id;
        $c = Category::create($data);
        return response()->json($c, 201);
    }

    public function update(Request $request, Category $category)
    {
        $user = $request->user();
        if ($user && $category->user_id && $category->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string', 'color' => 'nullable|string', 'icon' => 'nullable|string']);
        $category->update($data);
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $user = request()->user();
        if ($user && $category->user_id && $category->user_id !== $user->id) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $category->delete();
        return response()->json(null, 204);
    }
}
