<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));

        $categories = ExpenseCategory::query()
            ->withCount('expenses')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/ExpenseCategories', [
            'categories' => $categories,
            'filters'    => ['q' => $search],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:expense_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category = ExpenseCategory::create($validated);

        ActivityLog::record(
            action: 'create_expense_category',
            entityType: ExpenseCategory::class,
            entityId: $category->id,
            description: "Admin membuat kategori pengeluaran: {$category->name}"
        );

        return back()->with('status', "Kategori {$category->name} berhasil ditambahkan.");
    }

    public function update(Request $request, ExpenseCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:expense_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category->update($validated);

        ActivityLog::record(
            action: 'update_expense_category',
            entityType: ExpenseCategory::class,
            entityId: $category->id,
            description: "Admin memperbarui kategori pengeluaran: {$category->name}"
        );

        return back()->with('status', "Kategori {$category->name} berhasil diperbarui.");
    }

    public function destroy(ExpenseCategory $category): RedirectResponse
    {
        if ($category->expenses()->exists()) {
            return back()->withErrors(['name' => 'Kategori ini tidak dapat dihapus karena sudah dipakai dalam catatan pengeluaran.']);
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::record(
            action: 'delete_expense_category',
            entityType: ExpenseCategory::class,
            entityId: $category->id,
            description: "Admin menghapus kategori pengeluaran: {$name}"
        );

        return back()->with('status', "Kategori {$name} berhasil dihapus.");
    }
}
