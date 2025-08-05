<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::all();
        return view('status.index', compact('statuses'));
    }

    public function show($id)
    {
        $statuses = Status::findOrFail($id);
        return view('status.show', compact('statuses'));
    }

    public function create()
    {
        return view('status.create');
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_status' => 'required'
        ]);
        Status::create($validatedData);

        return redirect()->route('statuses.index')->with('success', 'Status created successfully');
    }

    public function edit($id)
    {
        $statuses = Status::findOrFail($id);
        return view('status.edit', compact('statuses'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_status' => 'required'
        ]);
        $status = Status::findOrFail($id);
        $status->update($validatedData);

        return redirect()->route('statuses.index')->with('success', 'Status updated successfully');
    }

    public function destroy($id)
    {
        $statuses = Status::findOrFail($id);
        $statuses->delete();
        return redirect()->route('statuses.index')->with('success', 'Status deleted successfully');
    }
}
