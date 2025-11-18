<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\Phong;
use App\Models\Khu;
use App\Models\TieuDe;
use App\Models\MucDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThongBaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Hiển thị danh sách thông báo
     */
    public function index(Request $request)
    {
        $query = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs.khu'])->orderBy('id', 'desc');

        // Tìm kiếm text chung
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tieuDe', fn($relationQuery) => $relationQuery->where('ten_tieu_de', 'like', "%$search%"))
                    ->orWhere('noi_dung', 'like', "%$search%")
                    ->orWhere('doi_tuong', 'like', "%$search%")
                    ->orWhereHas('mucDo', fn($relationQuery) => $relationQuery->where('ten_muc_do', 'like', "%$search%"))
                    ->orWhereHas('khus', fn($relationQuery) => $relationQuery->where('ten_khu', 'like', "%$search%"))
                    ->orWhereHas('phongs', fn($relationQuery) => $relationQuery->where('ten_phong', 'like', "%$search%"));
            });
        }

        // Bộ lọc modal
        if ($request->filled('doi_tuong')) {
            $query->where('doi_tuong', $request->doi_tuong);
        }
        if ($request->filled('muc_do')) {
            $query->where('muc_do_id', $request->muc_do);
        }
        if ($request->filled('phong_id')) {
            $query->whereHas('phongs', fn($q) => $q->where('id', $request->phong_id));
        }
        if ($request->filled('khu')) {
            $query->whereHas('khus', fn($q) => $q->where('ten_khu', $request->khu));
        }
        if ($request->filled('from_date')) {
            $query->whereDate('ngay_dang', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('ngay_dang', '<=', $request->to_date);
        }

        $thongbaos = $query->paginate(10)->appends($request->query());

        return view('thongbao.index', compact('thongbaos'));
    }



    /**
     * Form thêm thông báo
     */
    public function create()
    {
        $phongs = Phong::with('khu')->get();
        $khus = Khu::orderBy('ten_khu')->get();
        $tieuDes = TieuDe::all();
        $mucDos = MucDo::all();

        return view('thongbao.create', compact('phongs', 'khus', 'tieuDes', 'mucDos'));
    }

    /**
     * Lưu thông báo mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tieu_de_id' => 'required|exists:tieu_de,id',
            'muc_do_id' => 'nullable|exists:muc_do,id',
            'noi_dung' => 'required|string',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|string|max:255',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'khu_id' => 'nullable|array',
            'khu_id.*' => 'exists:khu,id',
            'phong_id' => 'nullable|array',
            'phong_id.*' => 'exists:phong,id',
        ]);

        // Upload ảnh
        if ($request->hasFile('anh')) {
            $data['anh'] = $request->file('anh')->store('thongbao/anh', 'public');
        }

        // Upload file PDF/Word/Excel
        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file')->store('thongbao/file', 'public');
        }

        //  Gắn người viết (user đang đăng nhập)
        $data['user_id'] = auth()->id();

        // Lưu thông báo
        $thongBao = ThongBao::create([
            'tieu_de_id' => $data['tieu_de_id'],
            'muc_do_id' => $data['muc_do_id'] ?? null,
            'noi_dung' => $data['noi_dung'],
            'ngay_dang' => $data['ngay_dang'],
            'doi_tuong' => $data['doi_tuong'],
            'anh' => $data['anh'] ?? null,
            'file' => $data['file'] ?? null,
            'user_id' => auth()->id(),
        ]);

        // Lưu quan hệ N-N
        if (!empty($data['khu_id'])) {
            $thongBao->khus()->sync($data['khu_id']);
        }
        if (!empty($data['phong_id'])) {
            $thongBao->phongs()->sync($data['phong_id']);
        }

        return redirect()->route('thongbao.index')->with('success', 'Thêm thông báo thành công!');
    }

    /**
     * Xem chi tiết thông báo
     */
    public function show(ThongBao $thongbao)
    {
        $thongbao->load(['tieuDe', 'mucDo', 'khus', 'phongs.khu']);
        return view('thongbao.show', compact('thongbao'));
    }

    /**
     * Form sửa thông báo
     */
    public function edit(ThongBao $thongbao)
    {
        $phongs = Phong::with('khu')->get();
        $khus = Khu::orderBy('ten_khu')->get();
        $tieuDes = TieuDe::all();
        $mucDos = MucDo::all();

        $selectedKhus = $thongbao->khus->pluck('id')->toArray();
        $selectedPhongs = $thongbao->phongs->pluck('id')->toArray();

        return view('thongbao.edit', compact(
            'thongbao',
            'phongs',
            'khus',
            'tieuDes',
            'mucDos',
            'selectedKhus',
            'selectedPhongs'
        ));
    }

    /**
     * Cập nhật thông báo
     */
    public function update(Request $request, ThongBao $thongbao)
    {
        $data = $request->validate([
            'tieu_de_id' => 'required|exists:tieu_de,id',
            'muc_do_id' => 'nullable|exists:muc_do,id',
            'noi_dung' => 'required|string',
            'ngay_dang' => 'required|date',
            'doi_tuong' => 'required|string|max:255',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'file' => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'khu_id' => 'nullable|array',
            'khu_id.*' => 'exists:khu,id',
            'phong_id' => 'nullable|array',
            'phong_id.*' => 'exists:phong,id',

        ]);
        $data['user_id'] = auth()->id();

        // Xử lý ảnh
        if ($request->hasFile('anh')) {
            if ($thongbao->anh && Storage::disk('public')->exists($thongbao->anh)) {
                Storage::disk('public')->delete($thongbao->anh);
            }
            $data['anh'] = $request->file('anh')->store('thongbao/anh', 'public');
        }

        // Xử lý file PDF/Word/Excel
        if ($request->hasFile('file')) {
            if ($thongbao->file && Storage::disk('public')->exists($thongbao->file)) {
                Storage::disk('public')->delete($thongbao->file);
            }
            $data['file'] = $request->file('file')->store('thongbao/file', 'public');
        }

        $thongbao->update([
            'tieu_de_id' => $data['tieu_de_id'],
            'muc_do_id' => $data['muc_do_id'] ?? null,
            'noi_dung' => $data['noi_dung'],
            'ngay_dang' => $data['ngay_dang'],
            'doi_tuong' => $data['doi_tuong'],
            'anh' => $data['anh'] ?? $thongbao->anh,
            'file' => $data['file'] ?? $thongbao->file,
            'user_id' => auth()->id(),
        ]);

        // Cập nhật N-N
        $thongbao->khus()->sync($data['khu_id'] ?? []);
        $thongbao->phongs()->sync($data['phong_id'] ?? []);

        return redirect()->route('thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
    }

    /**
     * Xóa thông báo
     */
    public function destroy(ThongBao $thongbao)
    {
        if ($thongbao->anh && Storage::disk('public')->exists($thongbao->anh)) {
            Storage::disk('public')->delete($thongbao->anh);
        }

        if ($thongbao->file && Storage::disk('public')->exists($thongbao->file)) {
            Storage::disk('public')->delete($thongbao->file);
        }

        $thongbao->delete();

        return redirect()->route('thongbao.index')->with('success', 'Xóa thông báo thành công!');
    }


    // =========================
    // CLIENT: Danh sách thông báo
    // =========================
    public function clientIndex()
    {
        $thongbaos = ThongBao::orderBy('ngay_dang', 'desc')->paginate(10);

        return view('public.thongbao.index', compact('thongbaos'));
    }

    // =========================
    // CLIENT: Chi tiết thông báo
    // =========================
    public function clientShow($id)
    {
        $thongbao = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs'])
            ->findOrFail($id);

        return view('public.thongbao.show', compact('thongbao'));
    }
}
