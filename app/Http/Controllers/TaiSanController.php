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
        $assetsInput = $request->input('assets');

        $baseRules = [
            'phong_id' => 'required|exists:phong,id',
            'tinh_trang' => 'nullable|string|max:255',
            'tinh_trang_hien_tai' => 'nullable|string|max:255',
            'redirect_to' => 'nullable|url',
        ];

        if (is_array($assetsInput)) {
            $request->validate($baseRules + [
                'assets' => 'required|array',
                'assets.*' => 'integer|min:1',
            ]);

            $assetQuantities = [];
            foreach ($assetsInput as $khoId => $qty) {
                if (!ctype_digit((string) $khoId)) {
                    continue;
                }
                $intQty = (int) $qty;
                if ($intQty > 0) {
                    $assetQuantities[(int) $khoId] = $intQty;
                }
            }

            if (empty($assetQuantities)) {
                return back()->withInput()->with('error', 'Vui lòng chọn ít nhất một tài sản với số lượng hợp lệ.');
            }

            DB::beginTransaction();
            try {
                $stocks = KhoTaiSan::whereIn('id', array_keys($assetQuantities))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($stocks->count() !== count($assetQuantities)) {
                    throw new \RuntimeException('Một số tài sản đã không còn khả dụng trong kho.');
                }

                foreach ($assetQuantities as $khoId => $qty) {
                    $kho = $stocks->get($khoId);
                    if ((int) $kho->so_luong < $qty) {
                        throw new \RuntimeException('Kho "' . ($kho->ten_tai_san ?? 'Không xác định') . '" không đủ số lượng (' . $kho->so_luong . ' < ' . $qty . ').');
                    }
                }

                $requestedCondition = $request->input('tinh_trang');
                $requestedCurrent = $request->input('tinh_trang_hien_tai');

                foreach ($assetQuantities as $khoId => $qty) {
                    $kho = $stocks->get($khoId);

                    $fallbackCondition = $requestedCondition ?: ($kho->tinh_trang ?? null);
                    $fallbackCurrent = $requestedCurrent ?: $fallbackCondition;

                    TaiSan::create([
                        'kho_tai_san_id' => $kho->id,
                        'ten_tai_san' => $kho->ten_tai_san,
                        'phong_id' => $request->input('phong_id'),
                        'so_luong' => $qty,
                        'tinh_trang' => $fallbackCondition,
                        'tinh_trang_hien_tai' => $fallbackCurrent,
                        'hinh_anh' => $kho->hinh_anh,
                    ]);

                    $kho->so_luong = max(0, (int) $kho->so_luong - $qty);
                    $kho->save();
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withInput()->with('error', $e->getMessage() ?: 'Không thể bổ sung tài sản cho phòng.');
            }

            $successMessage = 'Đã bổ sung ' . count($assetQuantities) . ' tài sản vào phòng và trừ kho!';
            $redirect = $request->input('redirect_to');
            if ($redirect && Str::startsWith($redirect, url('/'))) {
                return redirect($redirect)->with('success', $successMessage);
            }

            return redirect()->route('taisan.index')->with('success', $successMessage);
        }

        $validated = $request->validate($baseRules + [
            'kho_tai_san_id' => 'required|exists:kho_tai_san,id',
            'so_luong' => 'required|integer|min:1',
        ]);

        $kho = KhoTaiSan::findOrFail($validated['kho_tai_san_id']);

        if ($kho->so_luong < $validated['so_luong']) {
            return back()->withInput()->with('error', 'Số lượng trong kho không đủ!');
        }

        $defaultCondition = $validated['tinh_trang'] ?? ($kho->tinh_trang ?? null);
        $defaultCurrent = $validated['tinh_trang_hien_tai'] ?? $defaultCondition;

        TaiSan::create([
            'kho_tai_san_id' => $kho->id,
            'ten_tai_san' => $kho->ten_tai_san,
            'phong_id' => $validated['phong_id'],
            'so_luong' => $validated['so_luong'],
            'tinh_trang' => $defaultCondition,
            'tinh_trang_hien_tai' => $defaultCurrent,
            'hinh_anh' => $kho->hinh_anh,
        ]);

        $kho->decrement('so_luong', $validated['so_luong']);

        $redirect = $validated['redirect_to'] ?? null;
        if ($redirect && Str::startsWith($redirect, url('/'))) {
            return redirect($redirect)->with('success', 'Thêm tài sản thành công và trừ kho!');
        }

        return redirect()->route('taisan.index')->with('success', 'Thêm tài sản thành công và trừ kho!');
    }

    /** ✏️ Form chỉnh sửa tài sản phòng */
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
            return response()->json(['data' => '<p class="text-danger">Không tìm thấy tài sản.</p>']);
        }

        $html = view('taisan._modal', compact('taiSan'))->render();
        return response()->json(['data' => $html]);
    }
   public function related($loai_id)
{
    $khoTaiSans = KhoTaiSan::where('loai_id', $loai_id)
        ->select('id', 'ten_tai_san', 'so_luong', 'hinh_anh', 'tinh_trang')
        ->get()
        ->map(function ($item) {
            $item->hinh_anh = $item->hinh_anh 
                ? asset('storage/' . ltrim($item->hinh_anh, '/')) 
                : asset('uploads/default.png');
            return $item;
        });

    return response()->json($khoTaiSans); // phải trả JSON
}


    /**
     * Hiển thị tài sản theo từng phòng với 2 khu vực: tài sản chung và CSVC bàn giao cho slots
     */
    public function byPhong($phongId)
    {
        $phong = Phong::with(['khu'])->findOrFail($phongId);

        $roomAssets = $phong->taiSan()
            ->with('khoTaiSan')
            ->orderBy('ten_tai_san')
            ->get();

        $roomAssetFilterAccumulator = [];

        $roomAssets = $roomAssets->map(function ($asset) use (&$roomAssetFilterAccumulator) {
            $label = $asset->khoTaiSan->ten_tai_san ?? $asset->ten_tai_san ?? 'Không xác định';
            $normalized = Str::lower(trim($label));
            $filterKey = 'asset-' . md5($normalized);

            $asset->setAttribute('filter_label', $label);
            $asset->setAttribute('filter_key', $filterKey);

            if (!isset($roomAssetFilterAccumulator[$filterKey])) {
                $roomAssetFilterAccumulator[$filterKey] = [
                    'label' => $label,
                    'key' => $filterKey,
                    'item_count' => 0,
                    'total_quantity' => 0,
                ];
            }

            $roomAssetFilterAccumulator[$filterKey]['item_count']++;
            $roomAssetFilterAccumulator[$filterKey]['total_quantity'] += (int) ($asset->so_luong ?? 0);

            return $asset;
        });

        $totalRoomAssetQuantity = $roomAssets->sum(function ($asset) {
            return (int) ($asset->so_luong ?? 0);
        });

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
            'totalRoomAssetQuantity' => $totalRoomAssetQuantity,
        ]);
}


}
