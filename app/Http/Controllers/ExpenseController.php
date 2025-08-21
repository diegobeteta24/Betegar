<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', Expense::class);
        return Expense::where('technician_id', $user->id)->orderBy('id','desc')->get();
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', Expense::class);

        $data = $request->validate([
            'description' => ['required','string','min:3'],
            'amount' => ['required','numeric','min:0.01'],
            // Solo imágenes: jpg, jpeg, png
            'voucher' => ['required','image','mimes:jpg,jpeg,png','max:8192'],
        ]);

        $expense = Expense::create([
            'technician_id' => $user->id,
            'description' => $data['description'],
            'amount' => $data['amount'],
            'has_voucher' => false,
        ]);

        // guardar comprobante como imagen (o archivo) en images
        $file = $request->file('voucher');
        $path = $file->store('expenses', 'public');
        $expense->images()->create([
            'path' => $path,
            'size' => $file->getSize(),
            'tag'  => 'voucher',
        ]);

        $expense->update(['has_voucher' => true]);

        // Notificar a usuarios admin con suscripción push
        $fresh = $expense->fresh(['technician','images']);
        $admins = User::role('admin')->get();
        foreach($admins as $admin){
            try {
                $admin->notify(new ExpenseCreated(
                    expenseId: $fresh->id,
                    technicianName: $fresh->technician?->name ?? 'Técnico',
                    amount: (float)$fresh->amount,
                    description: $fresh->description,
                ));
            } catch(\Throwable $e) {
                // Evitar que un fallo de notificación rompa la creación
                \Log::warning('[EXPENSE][NOTIFY] fallo: '.$e->getMessage());
            }
        }

        return response()->json($fresh, 201);
    }
}
