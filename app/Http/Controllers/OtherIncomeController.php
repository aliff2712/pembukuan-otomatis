<?php

namespace App\Http\Controllers;

use App\Models\OtherIncome;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class OtherIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $incomes = OtherIncome::with('createdBy')
            ->orderBy('income_date', 'desc')
            ->paginate(15);

        return view('other-incomes.index', [
            'incomes' => $incomes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('other-incomes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
        ], [
            'income_date.required' => 'Tanggal harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'amount.required' => 'Jumlah harus diisi',
        ]);

        $validated['created_by'] = Auth::id();

        OtherIncome::create($validated);

        return Redirect::route('other-incomes.index')
            ->with('success', 'Income berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $income = OtherIncome::findOrFail($id);
        return view('other-incomes.show', ['income' => $income]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $income = OtherIncome::findOrFail($id);
        return view('other-incomes.edit', ['income' => $income]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $income = OtherIncome::findOrFail($id);

        if ($income->isPosted()) {
            return Redirect::back()
                ->with('error', 'Tidak bisa mengubah income yang sudah di-posting!');
        }

        $validated = $request->validate([
            'income_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'amount' => 'required|numeric|min:1',
        ]);

        $income->update($validated);

        return Redirect::route('other-incomes.show', $income)
            ->with('success', 'Income berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $income = OtherIncome::findOrFail($id);

        if ($income->isPosted()) {
            return Redirect::back()
                ->with('error', 'Tidak bisa menghapus income yang sudah di-posting!');
        }

        $income->delete();

        return Redirect::route('other-incomes.index')
            ->with('success', 'Income berhasil dihapus!');
    }
}
