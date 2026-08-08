<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\AssetCategory;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetCategoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AssetCategory::class);

        $categories = AssetCategory::withCount('assets')->orderBy('name')->get();

        return view('assets.categories', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = AssetCategory::create($request->validated());

        ActivityLogger::log('category', "Category created: {$category->name}");

        return back()->with('success', "Category {$category->name} created.");
    }

    public function update(UpdateCategoryRequest $request, AssetCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        ActivityLogger::log('category', "Category updated: {$category->name}");

        return back()->with('success', "Category {$category->name} updated.");
    }

    public function destroy(AssetCategory $category): RedirectResponse
    {
        if ($category->assets()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has assets.');
        }

        ActivityLogger::log('category', "Category deleted: {$category->name}");
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
