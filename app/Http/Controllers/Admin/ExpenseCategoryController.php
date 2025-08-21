<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::orderBy('name')->paginate(25);
        return view('admin.expense_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.expense_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:expense_categories,name']
        ]);
        ExpenseCategory::create($data);
        return redirect()->route('admin.expense-categories.index')
            ->with('sweet-alert', [
                'icon' => 'success',
                'title'=> 'Categoría creada',
                'timer'=> 1800,
                'showConfirmButton' => false,
            ]);
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('admin.expense_categories.edit', ['category' => $expenseCategory]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:expense_categories,name,'.$expenseCategory->id]
        ]);
        $expenseCategory->update($data);
        return redirect()->route('admin.expense-categories.index')
            ->with('sweet-alert', [
                'icon' => 'success',
                'title'=> 'Categoría actualizada',
                'timer'=> 1800,
                'showConfirmButton' => false,
            ]);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return back()->with('sweet-alert', [
            'icon' => 'success',
            'title'=> 'Categoría eliminada',
            'timer'=> 1500,
            'showConfirmButton' => false,
        ]);
    }

    /**
     * Formulario de importación (Excel) de categorías de gasto.
     */
    public function import(Request $request)
    {
        return view('admin.expense_categories.import');
    }
}
