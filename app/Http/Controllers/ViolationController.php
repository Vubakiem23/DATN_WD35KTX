<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\ViolationType;
use App\Models\SinhVien;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $q               = $request->input('q');
        $studentKeyword  = $request->input('student_keyword'); // <-- từ khóa tên/mã SV
        $typeId          = $request->input('type_id');
        $status          = $request->input('status');
        $dateFrom        = $request->input('date_from');
        $dateTo          = $request->input('date_to');

        $violations = \App\Models\Violation::with(['student', 'type'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('note', 'like', "%$q%")
                        ->orWhere('receipt_no', 'like', "%$q%");
                });
            })
            // 🔍 Lọc theo tên hoặc mã sinh viên gõ vào
            ->when($studentKeyword, function ($query) use ($studentKeyword) {
                $query->whereHas('student', function ($sub) use ($studentKeyword) {
                    $sub->where('ho_ten', 'like', "%$studentKeyword%")
                        ->orWhere('ma_sinh_vien', 'like', "%$studentKeyword%");
                });
            })
            ->when($typeId, function ($query) use ($typeId) {
                $query->where('violation_type_id', $typeId);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('occurred_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('occurred_at', '<=', $dateTo);
            })
            ->orderByDesc('occurred_at')
            ->paginate(10)
            ->appends($request->query());

        $types = \App\Models\ViolationType::select('id', 'name')->get();

        return view('vipham.index', compact(
            'violations',
            'types',
            'q',
            'studentKeyword',
            'typeId',
            'status',
            'dateFrom',
            'dateTo'
        ));
    }




    public function create(Request $request)
    {
        $students = SinhVien::orderBy('ho_ten')->select('id', 'ho_ten', 'ma_sinh_vien')->get();
        $types    = ViolationType::orderBy('name')->get();
        $defaultStudentId = $request->input('student_id'); // để từ trang SV nhảy sang sẽ giữ sẵn
        return view('vipham.create', compact('students', 'types', 'defaultStudentId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sinh_vien_id'      => 'required|exists:sinh_vien,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'occurred_at'       => 'required|date',
            'status'            => 'required|in:open,resolved',
            'penalty_amount'    => 'nullable|numeric',
            // 'receipt_no'        => 'nullable|string|max:100',
            'note'              => 'nullable|string',
            'image'             => 'nullable|image|max:2048', // thêm validate ảnh
        ]);

        unset($data['receipt_no']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('violations', 'public');
        }

        Violation::create($data);
        return redirect()->route('vipham.index')->with('success', 'Đã ghi vi phạm');
    }

    public function edit(Violation $vipham)
    {
        $students = SinhVien::orderBy('ho_ten')->select('id', 'ho_ten', 'ma_sinh_vien')->get();
        $types    = ViolationType::orderBy('name')->get();
        return view('vipham.edit', ['violation' => $vipham, 'students' => $students, 'types' => $types]);
    }

    public function update(Request $request, Violation $vipham)
    {
        $data = $request->validate([
            'sinh_vien_id'      => 'required|exists:sinh_vien,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'occurred_at'       => 'required|date',
            'status'            => 'required|in:open,resolved',
            'penalty_amount'    => 'nullable|numeric',
            // 'receipt_no'        => 'nullable|string|max:100',

            'note'              => 'nullable|string',
            'image'             => 'nullable|image|max:2048',
        ]);

        unset($data['receipt_no']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('violations', 'public');
        }

        $vipham->update($data);
        return redirect()->route('vipham.index')->with('success', 'Đã cập nhật vi phạm');
    }

    public function destroy(Violation $vipham)
    {
        $vipham->delete();
        return back()->with('success', 'Đã xóa vi phạm');
    }

    // PATCH /admin/vipham/{violation}/resolve
    public function resolve(Violation $violation)
    {
        $violation->update(['status' => 'resolved']);
        return back()->with('success', 'Đã đánh dấu đã xử lý');
    }
    public function show($id)
    {
        $violation = Violation::with(['student.phong.khu', 'student.slot.phong.khu', 'type'])->findOrFail($id);
        return view('vipham.show', compact('violation'));
    }
}
