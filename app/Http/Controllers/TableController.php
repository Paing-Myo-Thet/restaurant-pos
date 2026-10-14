<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tables = Table::latest()->get();

        return view('tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'status' => 'required|in:available,occupied,reserved'
        ]);

        $table->create($validated);
        
        return redirect()
        ->route('tables.index')
        ->with('success', 'Table ceated successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return view('tables.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'status' => 'required|in:available,occupied,reserved'
        ]);

        $table->update($validated);

        return redirect()
        ->route('tables.index')
        ->with('success', 'Table created Successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        if ($table->sales()->open()->exists()) {
            return redirect()
                ->route('tables.index')
                ->with('error', "{$table->name} has open orders and can't be deleted.");
        }

        $table->delete();

        return redirect()
        ->route('tables.index')
        ->with('success',"Tables deleted successfully.");

    }
}
