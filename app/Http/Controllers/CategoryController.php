<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $type = $request->string('type')->toString();

        $categories = $request->user()->categories()
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $incomeCount = $request->user()->categories()->where('type', 'income')->count();
        $expenseCount = $request->user()->categories()->where('type', 'expense')->count();

        return view('dashboard.categories.index', compact('categories', 'incomeCount', 'expenseCount'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $category = $request->user()->categories()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Categoría creada correctamente',
                'data' => [
                    'id' => $category->id,
                ],
            ], 201);
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Categoría creada correctamente');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $category->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Categoría actualizada correctamente',
                'data' => [
                    'id' => $category->id,
                ],
            ]);
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy(Request $request, Category $category): RedirectResponse|JsonResponse
    {
        $isInUse = $category->operations()->exists() || $category->goals()->exists();

        if ($isInUse) {
            $message = 'No se puede eliminar: la categoría está en uso.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 409);
            }

            return redirect()->back()->with('error', $message);
        }

        $category->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Categoría eliminada correctamente']);
        }

        return redirect()->route('dashboard.categories.index')->with('success', 'Categoría eliminada correctamente');
    }
}
