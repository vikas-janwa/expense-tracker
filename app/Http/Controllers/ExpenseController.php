<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request) {
        $user = auth()->user();

        $expenses = Expense::where('user_id', $user->id)->get();

        return view('expenses.index', compact('expenses'));
    }

    public function create(Request $request) {
        Log::info("Request recieved");
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|integer',
        ]);

        $user = Expense::create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'user_id' => $user->id
        ]);
        
        return redirect()->route('expenses.index');
    }

    public function getCreate(Request $request) {
        return view('expenses.create');
    }

    public function destroy(Expense $expense)
    {
        // Check if the authenticated user owns the expense
        if (auth()->user()->id !== $expense->user_id) {
            return redirect()->route('expenses.index')->with('error', 'You are not authorized to delete this expense.');
        }

        $expense->delete();
        return redirect()->route('expenses.index');
    }
}
 