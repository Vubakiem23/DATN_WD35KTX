<?php

namespace App\Http\Controllers;

use App\Models\Khu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Phong;

class KhuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $needsMigration = false;
        if (!Schema::hasTable('khu')) {
            $needsMigration = true;
            $khus = collect();
        } else {
            $khus = Khu::orderBy('ten_khu')->get();
        }
        return view('khu.index', compact('khus', 'needsMigration'));
    }

    public function create()
    {
        return view('khu.create');
    }

    public function edit(Khu $khu)
    {
        return view('khu.edit', compact('khu'));
    }

    public function show(Khu $khu)
    {
        // Thống kê phòng theo khu
        $phongs = Phong::where('khu_id', $khu->id)
            ->withCount([
                'slots as total_slots' => function($q){},
                'slots as used_slots' => function($q){ $q->whereNotNull('sinh_vien_id'); }
            ])
            ->orderBy('ten_phong')
            ->get();

        $total = $phongs->count();
        $noSlot = $phongs->where('total_slots', 0)->count();
        $empty = $phongs->where('total_slots', '>', 0)->where('used_slots', 0)->count();
        $full = $phongs->where('total_slots', '>', 0)->filter(function($p){ return (int)$p->used_slots >= (int)$p->total_slots; })->count();
        $partial = max(0, $total - $noSlot - $empty - $full);
        // Rooms that currently have available slots (>0)
        $hasAvailable = $phongs->where('total_slots', '>', 0)
            ->filter(function($p){ return (int)$p->used_slots < (int)$p->total_slots; })
            ->count();

        $stats = compact('total','empty','full','noSlot','partial','hasAvailable');
        return view('khu.show', compact('khu','phongs','stats'));
    }

    public function store(Request $request)
    {
        // Chuẩn hóa tên khu: loại khoảng trắng thừa, viết hoa để tránh trùng khác biệt hoa/thường
        $request->merge(['ten_khu' => trim((string)$request->input('ten_khu'))]);

        $data = $request->validate([
            'ten_khu' => ['required','string','max:100','unique:khu,ten_khu'],
            'gioi_tinh' => ['required','in:Nam,Nữ'],
            'mo_ta' => ['nullable','string','max:255'],
            'gia_moi_slot' => ['required','integer','min:0','max:1000000000'],
        ]);

        // Lưu tên khu ở dạng viết hoa để tránh trùng kiểu A/a
        $data['ten_khu'] = mb_strtoupper($data['ten_khu']);

        $khu = Khu::create($data);
        Log::info('Created Khu', ['khu_id' => $khu->id]);
        return redirect()->route('khu.index')->with('status', 'Tạo khu thành công');
    }

    public function update(Request $request, Khu $khu)
    {
        // Chuẩn hóa tên khu tương tự khi tạo
        $request->merge(['ten_khu' => trim((string)$request->input('ten_khu'))]);

        $data = $request->validate([
            'ten_khu' => ['required','string','max:100','unique:khu,ten_khu,' . $khu->id],
            'gioi_tinh' => ['required','in:Nam,Nữ'],
            'mo_ta' => ['nullable','string','max:255'],
            'gia_moi_slot' => ['required','integer','min:0','max:1000000000'],
        ]);

        $data['ten_khu'] = mb_strtoupper($data['ten_khu']);

        $khu->update($data);
        Log::info('Updated Khu', ['khu_id' => $khu->id]);

        return redirect()->route('khu.index')->with('status', 'Cập nhật khu thành công');
    }

}
