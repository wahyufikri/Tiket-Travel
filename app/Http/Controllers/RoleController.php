<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $allowedSortBy = ['name'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortBy) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        $roles = $query->paginate(10)->appends([
            'search' => $request->get('search'),
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]);

        return view('dashboard.manajemen.role.index', compact('roles', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        return view('dashboard.manajemen.role.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:60',
        ]);

        Role::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('manajemen-role.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $roles = Role::findOrFail($id);
        return view('dashboard.manajemen.role.edit', compact('roles'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:60',
        ]);

        $role = Role::findOrFail($id);
        $role->name = $validated['name'];
        $role->save();

        return redirect()->route('manajemen-role.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('manajemen-role.index')->with('success', 'Role berhasil dihapus.');
    }
}
