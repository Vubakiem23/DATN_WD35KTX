<?php

namespace App\Http\Controllers;

use App\Models\ViolationType;
use Illuminate\Http\Request;

class ViolationTypeController extends Controller
{
    public function index()
    {
        $types = ViolationType::orderBy('name')->paginate(15);
        return view('vipham.types.index', compact('types'));
    }

    public function create()
    {
        return view('vipham.types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:violation_types,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
        ViolationType::create($data);
        return redirect()->route('loaivipham.index')->with('success', 'Đã tạo loại vi phạm');
    }

    public function edit(ViolationType $loaivipham) // route-model: tham số phải trùng tên resource
    {
        return view('vipham.types.edit', ['type' => $loaivipham]);
    }

    public function update(Request $request, ViolationType $loaivipham)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:violation_types,code,' . $loaivipham->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
        $loaivipham->update($data);
        return redirect()->route('loaivipham.index')->with('success', 'Đã cập nhật');
    }

    public function destroy(ViolationType $loaivipham)
    {
        $loaivipham->delete();
        return back()->with('success', 'Đã xóa loại vi phạm');
    }
}
