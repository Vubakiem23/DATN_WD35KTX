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
        $q         = $request->input('q');
        $studentId = $request->input('student_id');
        $typeId    = $request->input('type_id');
        $status    = $request->input('status'); // open/resolved
        $dateFrom  = $request->input('date_from');
        $dateTo    = $request->input('date_to');

        $violations = Violation::with(['student','type'])
            ->when($q, fn($s)=>$s->where(function($w) use ($q){
                $w->where('note','like',"%$q%")
                  ->orWhere('receipt_no','like',"%$q%");
            }))
            ->when($studentId, fn($s)=>$s->where('sinh_vien_id',$studentId))
            ->when($typeId, fn($s)=>$s->where('violation_type_id',$typeId))
            ->when($status, fn($s)=>$s->where('status',$status))
            ->when($dateFrom, fn($s)=>$s->whereDate('occurred_at','>=',$dateFrom))
            ->when($dateTo, fn($s)=>$s->whereDate('occurred_at','<=',$dateTo))
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        $students = SinhVien::orderBy('ho_ten')->select('id','ho_ten','ma_sinh_vien')->get();
        $types    = ViolationType::orderBy('name')->get();

        return view('.vipham.index', compact(
            'violations','students','types','q','studentId','typeId','status','dateFrom','dateTo'
        ));
    }

    public function create(Request $request)
    {
        $students = SinhVien::orderBy('ho_ten')->select('id','ho_ten','ma_sinh_vien')->get();
        $types    = ViolationType::orderBy('name')->get();
        $defaultStudentId = $request->input('student_id'); // để từ trang SV nhảy sang sẽ giữ sẵn
        return view('vipham.create', compact('students','types','defaultStudentId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sinh_vien_id'      => 'required|exists:sinh_vien,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'occurred_at'       => 'required|date',
            'status'            => 'required|in:open,resolved',
            'penalty_amount'    => 'nullable|numeric',
            'receipt_no'        => 'nullable|string|max:100',
            'note'              => 'nullable|string',
        ]);

        Violation::create($data);
        return redirect()->route('vipham.index')->with('success','Đã ghi vi phạm');
    }

    public function edit(Violation $vipham)
    {
        $students = SinhVien::orderBy('ho_ten')->select('id','ho_ten','ma_sinh_vien')->get();
        $types    = ViolationType::orderBy('name')->get();
        return view('vipham.edit', ['violation'=>$vipham,'students'=>$students,'types'=>$types]);
    }

    public function update(Request $request, Violation $vipham)
    {
        $data = $request->validate([
            'sinh_vien_id'      => 'required|exists:sinh_vien,id',
            'violation_type_id' => 'required|exists:violation_types,id',
            'occurred_at'       => 'required|date',
            'status'            => 'required|in:open,resolved',
            'penalty_amount'    => 'nullable|numeric',
            'receipt_no'        => 'nullable|string|max:100',
            'note'              => 'nullable|string',
        ]);

        $vipham->update($data);
        return redirect()->route('vipham.index')->with('success','Đã cập nhật vi phạm');
    }

    public function destroy(Violation $vipham)
    {
        $vipham->delete();
        return back()->with('success','Đã xóa vi phạm');
    }

    // PATCH /admin/vipham/{violation}/resolve
    public function resolve(Violation $violation)
    {
        $violation->update(['status'=>'resolved']);
        return back()->with('success','Đã đánh dấu đã xử lý');
    }
}
