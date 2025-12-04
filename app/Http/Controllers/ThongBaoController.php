<?php

namespace App\Http\Controllers;

use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use App\Models\ThongBao;
use App\Models\Phong;
use App\Models\Khu;
use App\Models\TieuDe;
use App\Models\MucDo;
use App\Models\ThongBaoSinhVien;
use App\Models\ThongBaoSuCo;
use App\Models\ThongBaoPhongSv;
use App\Models\SuCo;
use App\Models\NotificationRead;
use App\Models\SinhVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->except([
            'clientIndex', 'clientShow', 'publicIndex', 'publicShow'
        ]);
    }

    // =========================
    // ADMIN: Quản lý thông báo
    // =========================

    public function index(Request $request)
    {
        $query = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs.khu'])
            ->orderBy('id', 'desc');

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('tieuDe', fn($rel) => $rel->where('ten_tieu_de', 'like', "%$search%"))
                  ->orWhere('noi_dung', 'like', "%$search%")
                  ->orWhere('doi_tuong', 'like', "%$search%")
                  ->orWhereHas('mucDo', fn($rel) => $rel->where('ten_muc_do', 'like', "%$search%"))
                  ->orWhereHas('khus', fn($rel) => $rel->where('ten_khu', 'like', "%$search%"))
                  ->orWhereHas('phongs', fn($rel) => $rel->where('ten_phong', 'like', "%$search%"));
            });
        }

        // Bộ lọc
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

    public function create()
    {
        $phongs = Phong::with('khu')->get();
        $khus = Khu::orderBy('ten_khu')->get();
        $tieuDes = TieuDe::all();
        $mucDos = MucDo::all();

        return view('thongbao.create', compact('phongs', 'khus', 'tieuDes', 'mucDos'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'tieu_de_id' => 'required|exists:tieu_de,id',
        'muc_do_id'  => 'nullable|exists:muc_do,id',
        'noi_dung'   => 'required|string',
        'ngay_dang'  => 'required|date',
        'doi_tuong'  => 'required|string|max:255',
        'anh'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'file'       => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        'khu_id'     => 'nullable|array',
        'khu_id.*'   => 'exists:khu,id',
        'phong_id'   => 'nullable|array',
        'phong_id.*' => 'exists:phong,id',
    ]);

    // Tách riêng ra trước
    $khuIds   = $data['khu_id']   ?? [];
    $phongIds = $data['phong_id'] ?? [];

    // Không cho chạy vào create()
    unset($data['khu_id'], $data['phong_id']);

    // Upload ảnh
    if ($request->hasFile('anh')) {
        $data['anh'] = $request->file('anh')->store('thongbao/anh', 'public');
    }

    // Upload file PDF/Word/Excel
    if ($request->hasFile('file')) {
        $data['file'] = $request->file('file')->store('thongbao/file', 'public');
    }

    // Gắn người viết
    $data['user_id'] = auth()->id();

    // Lưu thông báo
    $thongBao = ThongBao::create($data);

    // Lưu quan hệ N-N
    if (!empty($khuIds)) {
        $thongBao->khus()->sync($khuIds);
    }
    if (!empty($phongIds)) {
        $thongBao->phongs()->sync($phongIds);
    }

    return redirect()->route('thongbao.index')->with('success', 'Thêm thông báo thành công!');
}


    public function show(ThongBao $thongbao)
    {
        $thongbao->load(['tieuDe', 'mucDo', 'khus', 'phongs.khu']);
        return view('thongbao.show', compact('thongbao'));
    }

    public function edit(ThongBao $thongbao)
    {
        $phongs = Phong::with('khu')->get();
        $khus = Khu::orderBy('ten_khu')->get();
        $tieuDes = TieuDe::all();
        $mucDos = MucDo::all();

        $selectedKhus = $thongbao->khus->pluck('id')->toArray();
        $selectedPhongs = $thongbao->phongs->pluck('id')->toArray();

        return view('thongbao.edit', compact(
            'thongbao', 'phongs', 'khus', 'tieuDes', 'mucDos', 'selectedKhus', 'selectedPhongs'
        ));
    }

    public function update(Request $request, ThongBao $thongbao)
{
    $data = $request->validate([
        'tieu_de_id' => 'required|exists:tieu_de,id',
        'muc_do_id'  => 'nullable|exists:muc_do,id',
        'noi_dung'   => 'required|string',
        'ngay_dang'  => 'required|date',
        'doi_tuong'  => 'required|string|max:255',
        'anh'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'file'       => 'nullable|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        'khu_id'     => 'nullable|array',
        'khu_id.*'   => 'exists:khu,id',
        'phong_id'   => 'nullable|array',
        'phong_id.*' => 'exists:phong,id',
    ]);

    $khuIds   = $data['khu_id']   ?? [];
    $phongIds = $data['phong_id'] ?? [];

    unset($data['khu_id'], $data['phong_id']);

    $data['user_id'] = auth()->id();

    // Xử lý ảnh
    if ($request->hasFile('anh')) {
        if ($thongbao->anh && Storage::disk('public')->exists($thongbao->anh)) {
            Storage::disk('public')->delete($thongbao->anh);
        }
        $data['anh'] = $request->file('anh')->store('thongbao/anh', 'public');
    }

    // Xử lý file
    if ($request->hasFile('file')) {
        if ($thongbao->file && Storage::disk('public')->exists($thongbao->file)) {
            Storage::disk('public')->delete($thongbao->file);
        }
        $data['file'] = $request->file('file')->store('thongbao/file', 'public');
    }

    $thongbao->update($data);

    // Cập nhật N-N
    $thongbao->khus()->sync($khuIds);
    $thongbao->phongs()->sync($phongIds);

    return redirect()->route('thongbao.index')->with('success', 'Cập nhật thông báo thành công!');
}


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
        $user = Auth::user();
        $sinhVien = $user->sinhVien ?? SinhVien::where('email', $user->email)->first();

        $thongBaoSinhVien = collect([]);
        $thongBaoSuCo = collect([]);
        $thongBaoPhongSv = collect([]);
        $SuCo = collect([]);
        $HoaDonSlotPayment = collect([]);
        $HoaDonUtilitiesPayment = collect([]);

        if ($sinhVien) {
            $thongBaoSinhVien = ThongBaoSinhVien::where('sinh_vien_id', $sinhVien->id)
                ->orderBy('created_at', 'desc')->paginate(4);

            $thongBaoSuCo = ThongBaoSuCo::whereHas('su_co', fn($q) => $q->where('sinh_vien_id', $sinhVien->id))
                ->with('su_co.phong')->orderBy('ngay_tao', 'desc')->paginate(4);

            $thongBaoPhongSv = ThongBaoPhongSv::where('sinh_vien_id', $sinhVien->id)
                ->with('phong')->orderBy('created_at', 'desc')->paginate(4);

            $SuCo = SuCo::whereHas('sinhVien', fn($q) => $q->where('id', $sinhVien->id))
                ->with('phong')->orderBy('created_at', 'desc')->paginate(4);

            $HoaDonSlotPayment = HoaDonSlotPayment::whereHas('sinhVien', fn($q) => $q->where('id', $sinhVien->id))
                ->with('slot.phong')->orderBy('created_at', 'desc')->paginate(4);

            $HoaDonUtilitiesPayment = HoaDonUtilitiesPayment::whereHas('sinhVien', fn($q) => $q->where('id', $sinhVien->id))
                ->with('slot.phong')->orderBy('created_at', 'desc')->paginate(4);
        }

        // Đánh dấu đã đọc tất cả thông báo
        $userId = Auth::id();
        foreach (ThongBao::all() as $tb) {
            NotificationRead::firstOrCreate(
                ['user_id' => $userId, 'type' => 'thongbao', 'type_id' => $tb->id],
                ['read_at' => now()]
            );
        }

        foreach ($SuCo as $item) {
            NotificationRead::firstOrCreate(
                ['user_id' => $userId, 'type' => 'suco', 'type_id' => $item->id],
                ['read_at' => now()]
            );
        }

        foreach ($HoaDonSlotPayment as $item) {
            NotificationRead::firstOrCreate(
                ['user_id' => $userId, 'type' => 'slot', 'type_id' => $item->id],
                ['read_at' => now()]
            );
        }

        foreach ($HoaDonUtilitiesPayment as $item) {
            NotificationRead::firstOrCreate(
                ['user_id' => $userId, 'type' => 'utilities', 'type_id' => $item->id],
                ['read_at' => now()]
            );
        }

        $unread = NotificationRead::where('user_id', $userId)
            ->whereNull('read_at')->count();

        return view('client.thongbao.index', compact(
            'thongBaoSinhVien', 'thongBaoSuCo', 'thongBaoPhongSv',
            'sinhVien', 'SuCo', 'HoaDonSlotPayment', 'HoaDonUtilitiesPayment', 'unread'
        ));
    }

    // =========================
    // CLIENT: Chi tiết thông báo
    // =========================

    public function clientShow($id)
    {
        $thongbao = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs'])->findOrFail($id);

        $thongBaoMoi = ThongBao::with('tieuDe')
            ->where('id', '!=', $thongbao->id)
            ->orderBy('ngay_dang', 'desc')
            ->limit(5)
            ->get();

        return view('client.thongbao.show', compact('thongbao', 'thongBaoMoi'));
    }

    // =========================
    // PUBLIC: Danh sách & chi tiết thông báo
    // =========================

    public function publicIndex()
    {
        $thongbaos = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs'])
            ->orderBy('ngay_dang', 'desc')->paginate(10);

        return view('public.thongbao.index', compact('thongbaos'));
    }

    public function publicShow($id)
    {
        $thongbao = ThongBao::with(['tieuDe', 'mucDo', 'khus', 'phongs'])->findOrFail($id);

        $thongBaoMoi = ThongBao::with('tieuDe')
            ->where('id', '!=', $thongbao->id)
            ->orderBy('ngay_dang', 'desc')
            ->limit(5)
            ->get();

        return view('public.thongbao.show', compact('thongbao', 'thongBaoMoi'));
    }

    // =========================
    // AJAX: Load More Notifications
    // =========================

    public function loadMoreNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json([], 401);

        $sinhVien = $user->sinhVien ?? SinhVien::where('email', $user->email)->first();
        if (!$sinhVien) return response()->json([], 404);

        $type = $request->input('type');
        $offset = (int)$request->input('offset', 0);
        $limit = 4;

        switch ($type) {
            case 'sinhvien':
                $query = ThongBaoSinhVien::where('sinh_vien_id', $sinhVien->id)
                    ->orderBy('created_at', 'desc');
                break;
            case 'suco':
                $query = ThongBaoSuCo::whereHas('su_co', fn($q) => $q->where('sinh_vien_id', $sinhVien->id))
                    ->with('su_co.phong')
                    ->orderBy('ngay_tao', 'desc');
                break;
            case 'phong':
                $query = ThongBaoPhongSv::where('sinh_vien_id', $sinhVien->id)
                    ->with('phong')->orderBy('created_at', 'desc');
                break;
            case 'slot':
                $query = HoaDonSlotPayment::whereHas('sinhVien', fn($q) => $q->where('id', $sinhVien->id))
                    ->with('slot.phong')->orderBy('created_at', 'desc');
                break;
            case 'utilities':
                $query = HoaDonUtilitiesPayment::whereHas('sinhVien', fn($q) => $q->where('id', $sinhVien->id))
                    ->with('slot.phong')->orderBy('created_at', 'desc');
                break;
            default:
                return response()->json([]);
        }

        $notifications = $query->skip($offset)->take($limit)->get();
        return response()->json($notifications);
    }
}
