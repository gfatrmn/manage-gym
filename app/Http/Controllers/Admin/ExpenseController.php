<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ExpenseCategory;
use App\Models\ExpenseRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = ExpenseRecord::query()
            ->with(['category', 'user:id,name'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when(! empty($categoryId), function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when(! empty($startDate), function ($q) use ($startDate) {
                $q->whereDate('expense_date', '>=', $startDate);
            })
            ->when(! empty($endDate), function ($q) use ($endDate) {
                $q->whereDate('expense_date', '<=', $endDate);
            });

        $totalAmount = (int) (clone $query)->sum('amount');

        $expenses = $query->latest('expense_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = ExpenseCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Expenses', [
            'expenses'    => $expenses,
            'categories'  => $categories,
            'totalAmount' => $totalAmount,
            'filters'     => [
                'q'           => $search,
                'category_id' => $categoryId,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category_id'    => ['nullable', 'exists:expense_categories,id'],
            'category'       => ['nullable', 'string', 'max:100'],
            'amount'         => ['required', 'integer', 'min:1'],
            'expense_date'   => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string'],
        ]);

        $categoryName = $validated['category'] ?? null;
        if (! empty($validated['category_id']) && ! $categoryName) {
            $cat = ExpenseCategory::find($validated['category_id']);
            $categoryName = $cat?->name;
        }

        $expense = ExpenseRecord::create([
            'category_id'    => $validated['category_id'] ?? null,
            'user_id'        => session('auth.user_id') ?? session('auth.id') ?? auth()->id(),
            'title'          => $validated['title'],
            'category'       => $categoryName ?? 'Operasional',
            'amount'         => (int) $validated['amount'],
            'expense_date'   => $validated['expense_date'],
            'payment_method' => $validated['payment_method'] ?? 'cash',
            'notes'          => $validated['notes'] ?? null,
        ]);

        ActivityLog::record(
            action: 'create_expense',
            entityType: ExpenseRecord::class,
            entityId: $expense->id,
            description: "Admin mencatat pengeluaran: {$expense->title} (Rp " . number_format($expense->amount, 0, ',', '.') . ")"
        );

        return back()->with('status', "Pengeluaran {$expense->title} berhasil dicatat.");
    }

    public function update(Request $request, ExpenseRecord $expense): RedirectResponse
    {
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'category_id'    => ['nullable', 'exists:expense_categories,id'],
            'category'       => ['nullable', 'string', 'max:100'],
            'amount'         => ['required', 'integer', 'min:1'],
            'expense_date'   => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string'],
        ]);

        $categoryName = $validated['category'] ?? null;
        if (! empty($validated['category_id']) && ! $categoryName) {
            $cat = ExpenseCategory::find($validated['category_id']);
            $categoryName = $cat?->name;
        }

        $expense->update([
            'category_id'    => $validated['category_id'] ?? null,
            'title'          => $validated['title'],
            'category'       => $categoryName ?? $expense->category,
            'amount'         => (int) $validated['amount'],
            'expense_date'   => $validated['expense_date'],
            'payment_method' => $validated['payment_method'] ?? $expense->payment_method,
            'notes'          => $validated['notes'] ?? null,
        ]);

        ActivityLog::record(
            action: 'update_expense',
            entityType: ExpenseRecord::class,
            entityId: $expense->id,
            description: "Admin memperbarui catatan pengeluaran #{$expense->id}: {$expense->title}"
        );

        return back()->with('status', "Pengeluaran {$expense->title} berhasil diperbarui.");
    }

    public function destroy(ExpenseRecord $expense): RedirectResponse
    {
        $title = $expense->title;
        $id = $expense->id;
        $expense->delete();

        ActivityLog::record(
            action: 'delete_expense',
            entityType: ExpenseRecord::class,
            entityId: $id,
            description: "Admin menghapus catatan pengeluaran: {$title}"
        );

        return back()->with('status', "Pengeluaran {$title} berhasil dihapus.");
    }
}
