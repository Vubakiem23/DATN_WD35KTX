<?php

namespace App\Http\Controllers;

use App\Models\TaiSan;
use App\Models\KhoTaiSan;
use App\Models\Phong;
use App\Models\LoaiTaiSan;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaiSanController extends Controller
{
    /** 📋 Danh sách tài sản trong phòng */
    public function index(Request $request)
    {
        // Lấy danh sách phòng để filter nếu cần
        $phongs = Phong::orderBy('ten_phong')->get();

        // Lấy danh sách tài sản
        $listTaiSan = TaiSan::with(['khoTaiSan', 'phong', 'slots.sinhVien'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('khoTaiSan', function ($q) use ($search) {
                    $q->where('ma_tai_san', 'like', "%$search%")
                        ->orWhere('ten_tai_san', 'like', "%$search%");
                });
            })
            ->when($request->phong_id, function ($query, $phong_id) {
                $query->where('phong_id', $phong_id);
            })
            ->orderBy('created_at', 'desc') // ✅ mới thêm lên đầu
            ->paginate(6)
            ->withQueryString(); // giữ query string khi phân trang
        return view('taisan.index', compact('listTaiSan', 'phongs'));
    }
    public function create(Request $request)
    {
        $phongs = Phong::orderBy('ten_phong')->get();
        $loaiTaiSans = LoaiTaiSan::orderBy('ten_loai')->get();
        $taiSans = collect();
        $selectedLoai = null;
        $selectedTaiSan = null;
        // Nếu chọn loại tài sản
        if ($request->query('loai_id')) {
            $selectedLoai = LoaiTaiSan::find($request->query('loai_id'));
            if ($selectedLoai) {
                $taiSans = KhoTaiSan::where('loai_id', $selectedLoai->id)->get();
            }
        }
        // Nếu chọn tài sản
        if ($request->query('kho_tai_san_id')) {
            $selectedTaiSan = KhoTaiSan::find($request->query('kho_tai_san_id'));

            // Chuyển path thành URL đầy đủ để hiển thị
            if ($selectedTaiSan) {
                $selectedTaiSan->hinh_anh = $selectedTaiSan->hinh_anh
                    ? asset('storage/' . ltrim($selectedTaiSan->hinh_anh, '/'))
                    : asset('uploads/default.png'); // ảnh mặc định nếu không có
            }
        }
        return view('taisan.create', compact('phongs', 'loaiTaiSans', 'taiSans', 'selectedLoai', 'selectedTaiSan'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'phong_id' => ['required', 'integer', 'exists:phong,id'],
        'tai_san_ids' => ['required', 'array', 'min:1'],
        'tai_san_ids.*' => ['integer', 'exists:kho_tai_san,id'],
        'tinh_trang' => ['nullable', 'string', 'max:255'],
    ]);

    DB::beginTransaction();
    try {
        foreach ($validated['tai_san_ids'] as $khoId) {
            $kho = KhoTaiSan::lockForUpdate()->findOrFail($khoId);

            if ($kho->so_luong < 1) {
                throw new \Exception('Kho "' . $kho->ten_tai_san . '" không còn hàng.');
            }

            // ✅ Tạo mới tài sản với so_luong mặc định là 1
            TaiSan::create([
                'phong_id' => $validated['phong_id'],
                'kho_tai_san_id' => $kho->id,
                'ten_tai_san' => $kho->ten_tai_san,
                'hinh_anh' => $kho->hinh_anh,
                'so_luong' => 1, // ✅ thêm dòng này để tránh lỗi SQL 1364
                'tinh_trang' => $validated['tinh_trang'] ?? 'Bình thường',
                'tinh_trang_hien_tai' => 'Bình thường',
            ]);

            // ✅ Giảm số lượng trong kho
            $kho->decrement('so_luong', 1);
        }

        DB::commit();
        return redirect()->route('taisan.byPhong', ['phong' => $validated['phong_id']])
            ->with('success', 'Đã thêm tài sản vào phòng thành công!');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->withInput()->withErrors(['error' => $e->getMessage()]);
    }
}

    public function edit($id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $phongs = Phong::orderBy('ten_phong')->get();
        $khoTaiSans = KhoTaiSan::orderBy('ten_tai_san')->get();

        return view('taisan.edit', compact('taiSan', 'phongs', 'khoTaiSans'));
    }

    /** 🔄 Cập nhật tài sản phòng */
    public function update(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);

        $request->validate([
            'ten_tai_san' => 'required|string|max:255',
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'phong_id' => 'required|exists:phong,id',
            'so_luong' => 'required|integer|min:1',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
        ]);

        $kho = KhoTaiSan::findOrFail($request->kho_tai_san_id);
        $chenhLech = $request->so_luong - $taiSan->so_luong;

        if ($chenhLech > 0 && $kho->so_luong < $chenhLech) {
            return back()->with('error', 'Không đủ số lượng trong kho để tăng tài sản!');
        }

        if ($chenhLech > 0) {
            $kho->decrement('so_luong', $chenhLech);
        } elseif ($chenhLech < 0) {
            $kho->increment('so_luong', abs($chenhLech));
        }

        $taiSan->update([
            'kho_tai_san_id' => $kho->id,
            'ten_tai_san' => $kho->ten_tai_san,
            'phong_id' => $request->phong_id,
            'so_luong' => $request->so_luong,
            'tinh_trang' => $request->tinh_trang,
            'tinh_trang_hien_tai' => $request->tinh_trang_hien_tai,
        ]);

        return redirect()->route('taisan.index')->with('success', 'Cập nhật tài sản thành công!');
    }

    /** 🗑️ Xóa tài sản */
    public function destroy(Request $request, $id)
    {
        $taiSan = TaiSan::findOrFail($id);
        $kho = KhoTaiSan::find($taiSan->kho_tai_san_id);

        if ($kho) {
            $kho->increment('so_luong', $taiSan->so_luong);
        }

        $taiSan->delete();

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo && Str::startsWith($redirectTo, url('/'))) {
            return redirect($redirectTo)->with('success', 'Đã xóa tài sản khỏi phòng và hoàn kho thành công!');
        }

        return redirect()->route('taisan.index')->with('success', 'Đã xóa tài sản và hoàn kho thành công!');
    }

    /** 🖼️ Modal xem chi tiết */
    public function showModal($id)
{
    $taiSan = TaiSan::with(['phong', 'khoTaiSan'])->find($id);
    if (!$taiSan) {
        // Trả về HTML báo lỗi (không JSON)
        return response('<p class="text-danger">Không tìm thấy tài sản.</p>', 404)
               ->header('Content-Type', 'text/html; charset=utf-8');
    }

    // Nếu bạn có view partial resources/views/taisan/_modal.blade.php
    $html = view('taisan._modal', compact('taiSan'))->render();

    // Trả raw HTML (important)
    return response($html, 200)->header('Content-Type', 'text/html; charset=utf-8');
}

    public function related(Request $request, $loai_id)
    {
        // Lấy danh sách tài sản thuộc loại, còn hàng, chưa gán phòng
        $khoTaiSans = KhoTaiSan::where('loai_id', $loai_id)
            ->where('so_luong', '>', 0)
            ->select('id', 'ma_tai_san', 'ten_tai_san', 'hinh_anh', 'tinh_trang', 'so_luong')
            ->get()
            ->map(function ($item) {
                $item->hinh_anh = $item->hinh_anh
                    ? asset('storage/' . ltrim($item->hinh_anh, '/'))
                    : asset('uploads/default.png');
                return $item;
            });

        return response()->json($khoTaiSans);
    }

    /**
     * Hiển thị tài sản theo từng phòng với 2 khu vực: tài sản chung và CSVC bàn giao cho slots
     */
    public function byPhong($phongId)
    {
        $phong = Phong::with(['khu'])->findOrFail($phongId);

        $roomAssets = $phong->taiSan()
            ->with(['khoTaiSan', 'slots'])
            ->orderBy('ten_tai_san')
            ->get();

        $roomAssetsWithMeta = $roomAssets->map(function ($asset) {
            $label = $asset->khoTaiSan->ten_tai_san ?? $asset->ten_tai_san ?? 'Không xác định';
            $normalized = Str::lower(trim($label));
            $filterKey = 'asset-' . md5($normalized);
            $assignedQuantity = $asset->slots->sum(function ($slotItem) {
                return (int) ($slotItem->pivot->so_luong ?? 0);
            });
            $availableQuantity = max(0, (int) ($asset->so_luong ?? 0) - $assignedQuantity);

            $asset->setAttribute('filter_label', $label);
            $asset->setAttribute('filter_key', $filterKey);
            $asset->setAttribute('assigned_slot_quantity', $assignedQuantity);
            $asset->setAttribute('available_quantity', $availableQuantity);

            return $asset;
        });

        $totalCommonAssetQuantity = $roomAssetsWithMeta->sum(function ($asset) {
            return max(0, (int) ($asset->available_quantity ?? 0));
        });

        $totalSlotAssetQuantity = $roomAssetsWithMeta->sum(function ($asset) {
            return (int) ($asset->assigned_slot_quantity ?? 0);
        });

        $totalRoomInventoryQuantity = $totalCommonAssetQuantity + $totalSlotAssetQuantity;

        $roomAssets = $roomAssetsWithMeta->filter(function ($asset) {
            return (int) $asset->available_quantity > 0;
        })->values();

        $roomAssetFilterAccumulator = [];

        foreach ($roomAssets as $asset) {
            $filterKey = $asset->filter_key;
            $label = $asset->filter_label;

            if (!isset($roomAssetFilterAccumulator[$filterKey])) {
                $roomAssetFilterAccumulator[$filterKey] = [
                    'label' => $label,
                    'key' => $filterKey,
                    'item_count' => 0,
                    'total_quantity' => 0,
                ];
            }

            $roomAssetFilterAccumulator[$filterKey]['item_count']++;
            $roomAssetFilterAccumulator[$filterKey]['total_quantity'] += (int) ($asset->available_quantity ?? 0);
        }

        $roomAssetFilters = collect($roomAssetFilterAccumulator)
            ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $slots = Slot::with([
            'sinhVien',
            'taiSans' => function ($query) use ($phongId) {
                $query->with('khoTaiSan')
                    ->where('tai_san.phong_id', $phongId)
                    ->orderBy('ten_tai_san');
            }
        ])
            ->where('phong_id', $phongId)
            ->orderBy('ma_slot')
            ->get();

        $assignedWarehouseAssetIds = $roomAssets
            ->pluck('kho_tai_san_id')
            ->filter()
            ->unique();

        $warehouseAssets = KhoTaiSan::query()
            ->where('so_luong', '>', 0)
            ->when($assignedWarehouseAssetIds->isNotEmpty(), function ($query) use ($assignedWarehouseAssetIds) {
                $query->whereNotIn('id', $assignedWarehouseAssetIds);
            })
            ->orderBy('ten_tai_san')
            ->get();

        return view('phong.taisan', [
            'phong' => $phong,
            'roomAssets' => $roomAssets,
            'slots' => $slots,
            'warehouseAssets' => $warehouseAssets,
            'roomAssetFilters' => $roomAssetFilters,
            'totalCommonAssetQuantity' => $totalCommonAssetQuantity,
            'totalSlotAssetQuantity' => $totalSlotAssetQuantity,
            'totalRoomInventoryQuantity' => $totalRoomInventoryQuantity,
        ]);
    }
}
