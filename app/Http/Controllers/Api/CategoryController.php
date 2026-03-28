<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string', 'color' => 'nullable|string', 'icon' => 'nullable|string']);
        $c = Category::create($data);
        return response()->json($c, 201);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['name' => 'required|string', 'type' => 'nullable|string', 'color' => 'nullable|string', 'icon' => 'nullable|string']);
        $category->update($data);
        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
