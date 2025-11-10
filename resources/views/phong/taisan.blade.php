@extends('admin.layouts.admin')

@section('title', 'Tài sản phòng ' . $phong->ten_phong)

@section('content')
<div class="container page-container">
  @if(session('success'))
    @push('scripts')
      <script>
        window.addEventListener('DOMContentLoaded', () => {
          (window.showToast || window.alert)(@json(session('success')), 'success');
        });
      </script>
    @endpush
    <noscript>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    </noscript>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0 small">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @php
    $assetImagePlaceholder = asset('uploads/default.png');
    $roomAvatar = !empty($phong->hinh_anh) ? asset('storage/' . ltrim($phong->hinh_anh, '/')) : null;
    $roomAssetCount = $roomAssets->count();
    $slotCount = $slots->count();
    $warehouseAssetCount = $warehouseAssets->count();
    $totalSlotAssetQuantity = $slots->reduce(function ($carry, $slot) {
      return $carry + $slot->taiSans->sum(function ($item) {
        return (int) optional($item->pivot)->so_luong;
      });
    }, 0);
  @endphp

  <div class="row g-4 page-grid-top">
    <div class="col-12 col-xl-8">
      <div class="card asset-card shadow-sm border-0">
        <div class="card-header bg-white border-0">
          <div class="asset-card__header">
            <button class="card-toggle asset-card__collapse-trigger" type="button" data-bs-toggle="collapse" data-bs-target="#roomAssetsCollapse" data-toggle="collapse" data-target="#roomAssetsCollapse" aria-expanded="true" aria-controls="roomAssetsCollapse">
              <div class="toggle-content">
                <div class="section-heading">
                  <h4 class="section-heading__title mb-1">
                    <span class="section-heading__title-icon">
                      <svg class="icon icon--lg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.25 7.88a.75.75 0 0 1-.52 1.28H19.5v7a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-2a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-7h-1.76a.75.75 0 0 1-.52-1.28l8.25-7.88Z" />
                      </svg>
                    </span>
                    Tài sản phòng {{ $phong->ten_phong }}
                  </h4>
                  <div class="section-heading__meta">
                    <span class="section-heading__meta-item">
                      <svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 10.5a8.25 8.25 0 1 0-16.5 0c0 4.694 3.556 8.89 5.756 11.07a1.5 1.5 0 0 0 2.073 0C17.444 19.39 21 15.194 21 10.5Z" />
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11.25a1.125 1.125 0 1 1 0-2.25 1.125 1.125 0 0 1 0 2.25Z" />
                      </svg>
                      Khu: {{ optional($phong->khu)->ten_khu ?? 'Chưa cập nhật' }}
                    </span>
                    <span class="section-heading__meta-item">
                      <svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 20a4 4 0 0 0-3-3.874m-6 0A4 4 0 0 0 6 20m12 0h.01M6 20h.01M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-9a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm-12 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                      </svg>
                      Giới tính: {{ $phong->gioi_tinh ?? 'Không rõ' }}
                    </span>
                    <span class="section-heading__meta-item">
                      <svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18V9a1.5 1.5 0 0 1 1.5-1.5H6A2.25 2.25 0 0 1 8.25 9.75V18m-6 0h21m-21 0v-3.375A1.125 1.125 0 0 1 3.375 13.5H6m15.75 4.5V9a1.5 1.5 0 0 0-1.5-1.5H18A2.25 2.25 0 0 0 15.75 9.75V18M6 13.5h9.75" />
                      </svg>
                      Sức chứa: {{ $phong->suc_chua ?? '—' }} slot
                    </span>
                  </div>
                  <p class="section-heading__desc mb-0">Danh sách vật dụng sử dụng chung trong phòng</p>
                </div>
                <div class="toggle-meta">
                  <span class="badge badge-soft-primary">{{ $roomAssets->count() }} tài sản</span>
                  <svg class="icon icon--chevron toggle-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25 12 15.75 4.5 8.25" />
                  </svg>
                </div>
              </div>
            </button>
            <div class="asset-card__actions">
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddRoomAsset" data-toggle="modal" data-target="#modalAddRoomAsset" {{ $warehouseAssets->count() === 0 ? 'disabled' : '' }}>
                <svg class="icon icon--md me-1" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v6m3-3H9" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Bổ sung từ kho
              </button>
            </div>
          </div>
        </div>
        <div id="roomAssetsCollapse" class="collapse show">
          <div class="card-body asset-card__body asset-card__body--flush">
          @if($roomAssets->isEmpty())
            <div class="empty-state">
              <svg class="icon icon--xl mb-2 text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 11.25V9.75a2.25 2.25 0 0 1 2.25-2.25h10.5a2.25 2.25 0 0 1 2.25 2.25v1.5" />
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15.75V18a.75.75 0 0 1-.75.75H3.75A.75.75 0 0 1 3 18v-2.25a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 15.75Z" />
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18.75v1.5M18 18.75v1.5" />
              </svg>
              <p class="mb-0">Phòng chưa được cấp tài sản nào từ kho.</p>
            </div>
          @else
            @if($roomAssetFilters->isNotEmpty())
              <div
                class="asset-filter"
                data-role="room-asset-filter"
                data-total-rows="{{ $roomAssets->count() }}"
                data-total-quantity="{{ $totalRoomAssetQuantity }}"
              >
                <div class="asset-filter__menu" role="tablist">
                  <button
                    type="button"
                    class="asset-filter__pill active"
                    data-filter="all"
                    data-label="Tất cả"
                  >
                    Tất cả
                    <span class="badge">{{ $roomAssets->count() }}</span>
                  </button>
                  @foreach($roomAssetFilters as $filter)
                    <button
                      type="button"
                      class="asset-filter__pill"
                      data-filter="{{ $filter['key'] }}"
                      data-label="{{ $filter['label'] }}"
                    >
                      {{ $filter['label'] }}
                      <span class="badge">{{ $filter['item_count'] }}</span>
                    </button>
                  @endforeach
                </div>
                <div class="asset-filter__summary">
                  <span data-filter-summary>Đang hiển thị {{ $roomAssets->count() }} dòng · Tổng số lượng: {{ $totalRoomAssetQuantity }}</span>
                </div>
              </div>
            @endif
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Mã tài sản</th>
                    <th>Tên tài sản</th>
                    <th>Số lượng</th>
                    <th>Tình trạng chuẩn</th>
                    <th>Hiện tại</th>
                    <th>Ghi chú</th>
                    <th class="text-end">Hành động</th>
                  </tr>
                </thead>
                <tbody id="roomAssetsTableBody">
                  @foreach($roomAssets as $index => $asset)
                    <tr
                      data-filter-key="{{ $asset->filter_key }}"
                      data-filter-label="{{ $asset->filter_label }}"
                      data-row-quantity="{{ (int) ($asset->remaining_qty ?? 0) }}"
                    >
                      @php
                        $assetName = $asset->ten_tai_san ?? optional($asset->khoTaiSan)->ten_tai_san ?? $asset->filter_label;
                        $rawImagePath = $asset->hinh_anh ?? optional($asset->khoTaiSan)->hinh_anh;
                        $assetImageUrl = $rawImagePath
                          ? (filter_var($rawImagePath, FILTER_VALIDATE_URL)
                            ? $rawImagePath
                            : asset('storage/' . ltrim($rawImagePath, '/')))
                          : $assetImagePlaceholder;
                      @endphp
                      <td class="asset-order" data-initial="{{ $index + 1 }}">{{ $index + 1 }}</td>
                      <td class="asset-table__image">
                        <span class="asset-thumb">
                          <img src="{{ $assetImageUrl }}" alt="Ảnh {{ $assetName }}" class="asset-thumb__image">
                        </span>
                      </td>
                      <td>{{ $asset->khoTaiSan->ma_tai_san ?? '—' }}</td>
                      <td class="fw-semibold">{{ $asset->filter_label }}</td>
                      <td><span class="badge badge-soft-teal">{{ $asset->remaining_qty ?? 0 }}</span></td>
                      <td>{{ $asset->tinh_trang ?? 'Không rõ' }}</td>
                      <td>
                        @php
                          $statusRaw = $asset->tinh_trang_hien_tai ?? $asset->tinh_trang ?? null;
                          $normalized = $statusRaw ? mb_strtolower(trim($statusRaw), 'UTF-8') : '';
                          $displayStatus = 'Chưa cập nhật';
                          $badgeClass = 'bg-light';

                          if (in_array($normalized, ['mới', 'moi', 'new'])) {
                              $displayStatus = 'Mới';
                              $badgeClass = 'bg-success';
                          } elseif (in_array($normalized, ['bình thường', 'binh thuong', 'bt', 'hoàn thành', 'hoan thanh'])) {
                              $displayStatus = 'Bình thường';
                              $badgeClass = 'bg-success text-white';
                          } elseif (in_array($normalized, ['cũ', 'cu'])) {
                              $displayStatus = 'Cũ';
                              $badgeClass = 'bg-secondary';
                          } elseif (in_array($normalized, ['đang bảo trì', 'dang bao tri', 'bảo trì', 'bao tri', 'maintenance'])) {
                              $displayStatus = 'Đang bảo trì';
                              $badgeClass = 'bg-warning text-dark';
                          } elseif (in_array($normalized, ['hỏng', 'hong', 'đã hỏng', 'da hong', 'broken'])) {
                              $displayStatus = 'Hỏng';
                              $badgeClass = 'bg-danger';
                          }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $displayStatus }}</span>
                      </td>
                      <td>{{ $asset->ghi_chu ?? '—' }}</td>
                      <td>
                        <div class="asset-actions">
                          <form method="POST" action="{{ route('taisan.destroy', $asset->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài sản này khỏi phòng và hoàn kho?');">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                            <button type="submit" class="btn btn-sm btn-outline-danger asset-actions__btn">
                              <svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7.5h12" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 7.5V5.25A1.5 1.5 0 0 1 11.25 3.75h1.5a1.5 1.5 0 0 1 1.5 1.5V7.5" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7.5V19.5A1.5 1.5 0 0 0 9 21h6a1.5 1.5 0 0 0 1.5-1.5V7.5" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 11.25v6" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 11.25v6" />
                              </svg>
                              <span>Xóa</span>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
          </div>
        </div>
      </div>
      <div class="card asset-card shadow-sm border-0 mt-4">
        <div class="card-header bg-white border-0">
          <div class="asset-card__header">
            <button class="card-toggle asset-card__collapse-trigger" type="button" data-bs-toggle="collapse" data-bs-target="#slotAssetsCollapse" data-toggle="collapse" data-target="#slotAssetsCollapse" aria-expanded="true" aria-controls="slotAssetsCollapse">
              <div class="toggle-content">
                <div class="section-heading">
                  <h5 class="section-heading__title mb-1">Cơ sở vật chất bàn giao cho slots</h5>
                  <p class="section-heading__subtitle mb-0">Phòng {{ $phong->ten_phong }}</p>
                  <p class="section-heading__desc mb-0">Theo dõi các vật dụng đã cấp riêng cho từng chỗ ở</p>
                </div>
                <div class="toggle-meta">
                  <span class="badge badge-soft-primary">{{ $slots->count() }} slot</span>
                  <svg class="icon icon--chevron toggle-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 8.25 12 15.75 4.5 8.25" />
                  </svg>
                </div>
              </div>
            </button>
            <div class="asset-card__actions">
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSlotAsset" data-toggle="modal" data-target="#modalAddSlotAsset" {{ ($slots->count() === 0 || $warehouseAssets->count() === 0) ? 'disabled' : '' }}>
                <svg class="icon icon--md me-1" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v6m3-3H9" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Bổ sung cho slot
              </button>
            </div>
          </div>
        </div>
        <div id="slotAssetsCollapse" class="collapse show">
          <div class="card-body asset-card__body">
          @if($slots->isEmpty())
            <div class="empty-state">
              <svg class="icon icon--xl mb-2 text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18V9a1.5 1.5 0 0 1 1.5-1.5H6A2.25 2.25 0 0 1 8.25 9.75V18m-6 0h21m-21 0v-3.375A1.125 1.125 0 0 1 3.375 13.5H6m15.75 4.5V9a1.5 1.5 0 0 0-1.5-1.5H18A2.25 2.25 0 0 0 15.75 9.75V18M6 13.5h9.75" />
              </svg>
              <p class="mb-0">Phòng chưa có slot nào được tạo.</p>
            </div>
          @else
            <div class="slot-grid">
              @foreach($slots as $slot)
                @php
                  $slotTotal = $slot->taiSans->sum(function ($item) {
                      return (int) optional($item->pivot)->so_luong;
                  });
                @endphp
                <div class="slot-card">
                  <div class="slot-card__header">
                    <div>
                      <div class="slot-code">{{ $slot->ma_slot }}</div>
                        @if($slot->sinhVien)
                        <div class="slot-meta slot-meta--student">
                          <svg class="icon icon--sm" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 20.625a8.25 8.25 0 0 1 15 0" />
                          </svg>
                          <div class="slot-holder">
                            <span class="slot-holder__name">{{ $slot->sinhVien->ho_ten }}</span>
                            @if(!empty($slot->sinhVien->ma_sinh_vien))
                              <span class="slot-holder__id">MSSV: {{ $slot->sinhVien->ma_sinh_vien }}</span>
                            @endif
                          </div>
                        </div>
                        @else
                        <div class="slot-meta slot-meta--empty text-muted">
                          <span class="badge badge-soft-gray">Chưa có sinh viên</span>
                        </div>
                        @endif
                    </div>
                    <span class="badge badge-soft-teal">{{ $slotTotal }} món</span>
                  </div>
                  <div class="slot-card__body">
                    @if($slot->taiSans->isEmpty())
                      <div class="empty-state-min">Chưa bàn giao CSVC cho slot này.</div>
                    @else
                      <ul class="slot-assets-list">
                        @foreach($slot->taiSans as $asset)
                          @php
                            $inventoryAsset = $asset->khoTaiSan;
                            $assetImage = $inventoryAsset && !empty($inventoryAsset->hinh_anh)
                              ? asset('storage/' . $inventoryAsset->hinh_anh)
                              : null;
                            $assetName = $inventoryAsset->ten_tai_san ?? $asset->ten_tai_san;
                            $assetCode = $inventoryAsset->ma_tai_san ?? ($asset->ma ?? '—');
                          @endphp
                          <li class="slot-asset">
                            <div class="slot-asset__content">
                              <div class="slot-asset__thumb {{ $assetImage ? 'has-image' : 'is-placeholder' }}">
                                @if($assetImage)
                                  <img src="{{ $assetImage }}" alt="Hình ảnh {{ $assetName }}">
                                @else
                                  <span class="slot-asset__thumb-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                      <path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="m4 16 4.586-4.586a2 2 0 0 1 2.828 0L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="m14 14 1.586-1.586a2 2 0 0 1 2.828 0L20 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                      <path d="M10 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z" fill="currentColor" />
                                    </svg>
                                  </span>
                                @endif
                              </div>
                              <div class="slot-asset__details">
                                <div class="slot-asset-name">{{ $assetName }}</div>
                                <div class="slot-asset-code text-muted small">Mã: {{ $assetCode }}</div>
                                <div class="slot-asset-meta text-muted small">
                                  @php
                                    $statusRaw = $asset->tinh_trang_hien_tai ?? $asset->tinh_trang ?? null;
                                    $normalized = $statusRaw ? mb_strtolower(trim($statusRaw), 'UTF-8') : '';
                                    $currentText = 'Chưa cập nhật';
                                    if (in_array($normalized, ['mới', 'moi', 'new'])) {
                                        $currentText = 'Mới';
                                    } elseif (in_array($normalized, ['bình thường', 'binh thuong', 'bt', 'hoàn thành', 'hoan thanh'])) {
                                        $currentText = 'Bình thường';
                                    } elseif (in_array($normalized, ['cũ', 'cu'])) {
                                        $currentText = 'Cũ';
                                    } elseif (in_array($normalized, ['đang bảo trì', 'dang bao tri', 'bảo trì', 'bao tri', 'maintenance'])) {
                                        $currentText = 'Đang bảo trì';
                                    } elseif (in_array($normalized, ['hỏng', 'hong', 'đã hỏng', 'da hong', 'broken'])) {
                                        $currentText = 'Hỏng';
                                    }
                                  @endphp
                                  Chuẩn: {{ $asset->tinh_trang ?? 'Không rõ' }} · Hiện tại: {{ $currentText }}
                                </div>
                              </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                              <span class="badge badge-soft-primary">x{{ optional($asset->pivot)->so_luong ?? 0 }}</span>
                              <button
                                type="button"
                                class="btn btn-sm btn-outline-danger asset-actions__btn"
                                data-role="remove-slot-asset"
                                data-url="{{ route('slots.returnAssetToWarehouse', $slot->id) }}"
                                data-asset-id="{{ $asset->id }}"
                                title="Xóa tài sản khỏi slot"
                              >
                                <svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7.5h12" />
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 7.5V5.25A1.5 1.5 0 0 1 11.25 3.75h1.5a1.5 1.5 0 0 1 1.5 1.5V7.5" />
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7.5V19.5A1.5 1.5 0 0 0 9 21h6a1.5 1.5 0 0 0 1.5-1.5V7.5" />
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 11.25v6" />
                                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 11.25v6" />
                                </svg>
                                <!-- <span>Xóa</span> -->
                              </button>
                            </div>
                          </li>
                        @endforeach
                      </ul>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-4">
      <div class="card shadow-sm border-0 page-summary">
        <div class="card-body">
          <div class="page-summary__top">
            <div class="page-summary__identity">
              <div class="room-avatar">
                @if($roomAvatar)
                  <img src="{{ $roomAvatar }}" alt="Ảnh phòng {{ $phong->ten_phong }}" class="room-avatar__image">
                @else
                  <span class="room-avatar__placeholder" aria-hidden="true">
                    <svg class="room-avatar__icon" viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                      <path fill="currentColor" d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.25 7.88a.75.75 0 0 1-.52 1.28H19.5v7a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-2a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-7h-1.76a.75.75 0 0 1-.52-1.28l8.25-7.88Z" />
                    </svg>
                  </span>
                @endif
              </div>
              <div>
                <h3 class="page-summary__title mb-1">Tài sản phòng {{ $phong->ten_phong }}</h3>
                <div class="page-summary__meta">
                  <span class="page-summary__meta-item">
                    <svg class="icon icon--sm text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 10.5a8.25 8.25 0 1 0-16.5 0c0 4.694 3.556 8.89 5.756 11.07a1.5 1.5 0 0 0 2.073 0C17.444 19.39 21 15.194 21 10.5Z" />
                      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11.25a1.125 1.125 0 1 1 0-2.25 1.125 1.125 0 0 1 0 2.25Z" />
                    </svg>
                    Khu {{ optional($phong->khu)->ten_khu ?? 'chưa cập nhật' }}
                  </span>
                  <span class="page-summary__meta-item">
                    <svg class="icon icon--sm text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 20a4 4 0 0 0-3-3.874m-6 0A4 4 0 0 0 6 20m12 0h.01M6 20h.01M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-9a2 2 0 1 1 0-4 2 2 0 0 1 0 4Zm-12 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" />
                    </svg>
                    Giới tính {{ $phong->gioi_tinh ?? 'không rõ' }}
                  </span>
                  <span class="page-summary__meta-item">
                    <svg class="icon icon--sm text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                      <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18V9a1.5 1.5 0 0 1 1.5-1.5H6A2.25 2.25 0 0 1 8.25 9.75V18m-6 0h21m-21 0v-3.375A1.125 1.125 0 0 1 3.375 13.5H6m15.75 4.5V9a1.5 1.5 0 0 0-1.5-1.5H18A2.25 2.25 0 0 0 15.75 9.75V18M6 13.5h9.75" />
                    </svg>
                    Sức chứa {{ $phong->suc_chua ?? '—' }} slot
                  </span>
                </div>
              </div>
            </div>
            <div class="page-summary__actions">
              <a href="{{ route('phong.show', $phong->id) }}" class="btn btn-outline-primary">
                <svg class="icon icon--md" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 3.75h9a1.5 1.5 0 0 1 1.5 1.5V18.75" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 3.75v16.5" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 7.5H18a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-.75.75h-2.25" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 12h.008" />
                </svg>
                Tổng quan phòng
              </a>
              <a href="{{ route('phong.index') }}" class="btn btn-light border">
                <svg class="icon icon--md" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6 4.5 12l6 6" />
                  <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12h15" />
                </svg>
                Danh sách phòng
              </a>
            </div>
          </div>
          <div class="page-summary__stats">
            <div class="stat-pill">
              <span class="stat-pill__label">Tài sản chung</span>
              <span class="stat-pill__value">{{ number_format($roomAssetCount) }}</span>
              <span class="stat-pill__hint">Số dòng hiện có</span>
            </div>
            <div class="stat-pill">
              <span class="stat-pill__label">Số lượng hiện hữu</span>
              <span class="stat-pill__value">{{ number_format($totalRoomAssetQuantity) }}</span>
              <span class="stat-pill__hint">Món được cấp chung phòng</span>
            </div>
            <div class="stat-pill">
              <span class="stat-pill__label">Slot đã bàn giao</span>
              <span class="stat-pill__value">{{ number_format($slotCount) }}</span>
              <span class="stat-pill__hint">{{ number_format($totalSlotAssetQuantity) }} món tại slot</span>
            </div>
            <div class="stat-pill">
              <span class="stat-pill__label">Kho khả dụng</span>
              <span class="stat-pill__value">{{ number_format($warehouseAssetCount) }}</span>
              <span class="stat-pill__hint">Món sẵn sàng để cấp phát</span>
            </div>
          </div>
          </div>
          </div>
        </div>
  </div>
</div>
{{-- Modal thêm tài sản phòng --}}
<div class="modal fade" id="modalAddRoomAsset" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="POST" action="{{ route('taisan.store') }}">
      @csrf
      <input type="hidden" name="phong_id" value="{{ $phong->id }}">
      <input type="hidden" name="redirect_to" value="{{ route('taisan.byPhong', $phong->id) }}">
      <input type="hidden" name="form_origin" value="room_assets">
      <div class="modal-header">
        <h5 class="modal-title">Bổ sung tài sản cho phòng {{ $phong->ten_phong }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        @if($warehouseAssets->isEmpty())
          <div class="alert alert-warning mb-0">Kho hiện chưa có tài sản khả dụng.</div>
        @else
          @php
            $roomFormOrigin = old('form_origin');
            $oldRoomAssetsInput = $roomFormOrigin === 'room_assets' ? old('assets', []) : [];
            $oldRoomAssetsInput = is_array($oldRoomAssetsInput)
              ? array_filter($oldRoomAssetsInput, function ($qty) {
                  return is_numeric($qty) && (int) $qty > 0;
                })
              : [];
          @endphp
          <div class="asset-modal">
            <div class="row g-4">
              <div class="col-lg-7">
                <div class="asset-modal__column">
                  <h6 class="asset-modal__heading">Chọn tài sản từ kho</h6>
                  <div class="asset-modal__search mb-3">
                    <input
                      type="search"
                      class="form-control form-control-sm"
                      placeholder="Tìm kiếm tài sản..."
                      data-role="asset-search"
                      autocomplete="off"
                    >
                  </div>
                  <div class="asset-option-list" data-role="asset-picker">
                @foreach($warehouseAssets as $item)
                      @php
                        $itemImage = $item->hinh_anh
                          ? asset('storage/' . ltrim($item->hinh_anh, '/'))
                          : $assetImagePlaceholder;
                        $oldQty = $oldRoomAssetsInput[$item->id] ?? null;
                      @endphp
                      <div
                        class="asset-option"
                        data-role="asset-item"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->ten_tai_san }}"
                        data-code="{{ $item->ma_tai_san ?? 'N/A' }}"
                        data-stock="{{ (int) $item->so_luong }}"
                        data-condition="{{ $item->tinh_trang ?? 'Không rõ' }}"
                        data-image="{{ $itemImage }}"
                        data-old-qty="{{ $oldQty ?? '' }}"
                      >
                        <div class="asset-option__body">
                          <div class="asset-option__thumb">
                            <img src="{{ $itemImage }}" alt="{{ $item->ten_tai_san }}">
                          </div>
                          <div class="asset-option__details">
                            <div class="asset-option__title">{{ $item->ten_tai_san }}</div>
                            <div class="asset-option__meta text-muted small">
                              Mã: {{ $item->ma_tai_san ?? 'N/A' }} · Tồn kho: {{ (int) $item->so_luong }}
                            </div>
                            <div class="asset-option__condition text-muted small">
                              Tình trạng: {{ $item->tinh_trang ?? 'Không rõ' }}
                            </div>
                          </div>
                          <div class="asset-option__actions">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-role="asset-toggle">
                              Chọn
                            </button>
                          </div>
                        </div>
                      </div>
                @endforeach
            </div>
                  <p class="text-muted small mt-3 d-none" data-role="asset-search-empty">
                    Không tìm thấy tài sản phù hợp.
                  </p>
                </div>
              </div>
              <div class="col-lg-5">
                <div class="asset-modal__column">
                  <h6 class="asset-modal__heading">Tài sản đã chọn</h6>
                  <div class="selected-assets" data-role="selected-assets">
                    <p class="text-muted small mb-0" data-role="empty-state">Chưa chọn tài sản nào.</p>
            </div>
                  <p class="text-muted small mt-3 mb-0">
                    Tình trạng được lấy trực tiếp từ kho và không thể chỉnh sửa tại đây.
                  </p>
            </div>
            </div>
            </div>
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-primary" data-role="asset-submit" {{ $warehouseAssets->isEmpty() ? 'disabled' : '' }} disabled>Lưu bổ sung</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal bổ sung tài sản cho slot --}}
<div class="modal fade" id="modalAddSlotAsset" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="POST" action="{{ route('slots.importFromWarehouse') }}">
      @csrf
      <input type="hidden" name="form_origin" value="slot_assets">
      <div class="modal-header">
        <h5 class="modal-title">Bổ sung tài sản cho slot</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        @if($slots->isEmpty())
          <div class="alert alert-warning mb-0">Phòng chưa có slot nào để bàn giao tài sản.</div>
        @elseif($warehouseAssets->isEmpty())
          <div class="alert alert-warning mb-0">Kho hiện chưa có tài sản khả dụng.</div>
        @else
          @php
            $slotFormOrigin = old('form_origin');
            $oldSlotAssetsInput = $slotFormOrigin === 'slot_assets' ? old('assets', []) : [];
            $oldSlotAssetsInput = is_array($oldSlotAssetsInput)
              ? array_filter($oldSlotAssetsInput, function ($qty) {
                  return is_numeric($qty) && (int) $qty > 0;
                })
              : [];
          @endphp
          <div class="row g-3 mb-3">
            <div class="col-12 col-lg-6 col-xl-5 slot-select-wrapper">
              <label class="form-label slot-select-label" for="slot_select">Chọn slot</label>
              <select class="form-select asset-slot-select" name="slot_id" id="slot_select" required data-role="slot-select">
                <option value="">-- Chọn slot --</option>
                @foreach($slots as $slot)
                  <option value="{{ $slot->id }}" {{ (string) old('slot_id') === (string) $slot->id ? 'selected' : '' }}>
                    {{ $slot->ma_slot }}
                    @if($slot->sinhVien)
                      - {{ $slot->sinhVien->ho_ten }}
                    @endif
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="asset-modal asset-modal--compact">
            <div class="row g-4">
              <div class="col-lg-7">
                <div class="asset-modal__column">
                  <h6 class="asset-modal__heading">Danh sách tài sản trong kho</h6>
                  <div class="asset-modal__search mb-3">
                    <input
                      type="search"
                      class="form-control form-control-sm"
                      placeholder="Tìm kiếm tài sản..."
                      data-role="asset-search"
                      autocomplete="off"
                    >
                  </div>
                  <div class="asset-option-list" data-role="asset-picker">
                @foreach($warehouseAssets as $item)
                      @php
                        $itemImage = $item->hinh_anh
                          ? asset('storage/' . ltrim($item->hinh_anh, '/'))
                          : $assetImagePlaceholder;
                        $oldQty = $oldSlotAssetsInput[$item->id] ?? null;
                      @endphp
                      <div
                        class="asset-option"
                        data-role="asset-item"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->ten_tai_san }}"
                        data-code="{{ $item->ma_tai_san ?? 'N/A' }}"
                        data-stock="{{ (int) $item->so_luong }}"
                        data-condition="{{ $item->tinh_trang ?? 'Không rõ' }}"
                        data-image="{{ $itemImage }}"
                        data-old-qty="{{ $oldQty ?? '' }}"
                      >
                        <div class="asset-option__body">
                          <div class="asset-option__thumb">
                            <img src="{{ $itemImage }}" alt="{{ $item->ten_tai_san }}">
                          </div>
                          <div class="asset-option__details">
                            <div class="asset-option__title">{{ $item->ten_tai_san }}</div>
                            <div class="asset-option__meta text-muted small">
                              Mã: {{ $item->ma_tai_san ?? 'N/A' }} · Tồn kho: {{ (int) $item->so_luong }}
                            </div>
                            <div class="asset-option__condition text-muted small">
                              Tình trạng: {{ $item->tinh_trang ?? 'Không rõ' }}
                            </div>
                          </div>
                          <div class="asset-option__actions">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-role="asset-toggle">
                              Chọn
                            </button>
                          </div>
                        </div>
                      </div>
                @endforeach
            </div>
                  <p class="text-muted small mt-3 d-none" data-role="asset-search-empty">
                    Không tìm thấy tài sản phù hợp.
                  </p>
            </div>
                </div>
              <div class="col-lg-5">
                <div class="asset-modal__column">
                  <h6 class="asset-modal__heading">Tài sản sẽ bàn giao</h6>
                  <div class="selected-assets" data-role="selected-assets">
                    <p class="text-muted small mb-0" data-role="empty-state">Chưa chọn tài sản nào.</p>
              </div>
                  <p class="text-muted small mt-3 mb-0">
                    Mỗi tài sản được bàn giao 1 món theo tồn kho hiện tại.
                  </p>
            </div>
            </div>
            </div>
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-primary" data-role="asset-submit" {{ ($slots->isEmpty() || $warehouseAssets->isEmpty()) ? 'disabled' : '' }} disabled>Lưu bổ sung</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
  body.nav-md .container.body .right_col {
    padding-top: 18px;
  }
  .page-container {
    margin-top: 0;
    padding-top: 0;
  }
  .page-grid-top {
    margin-bottom: 1.5rem;
  }
  .asset-modal {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  #modalAddSlotAsset .slot-select-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
  }
  #modalAddSlotAsset .slot-select-label {
    font-weight: 600;
    color: #4338ca;
    margin-bottom: 0;
    letter-spacing: .01em;
  }
  #modalAddSlotAsset .asset-slot-select {
    padding: 0.65rem 1rem;
    border-radius: 14px;
    border: 1px solid rgba(99, 102, 241, 0.28);
    box-shadow: 0 10px 28px rgba(79, 70, 229, 0.12);
    font-weight: 600;
    color: #1e1b4b;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  #modalAddSlotAsset .asset-slot-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.18);
    outline: none;
  }
  #modalAddSlotAsset .asset-slot-select option {
    font-weight: 500;
  }
  .asset-modal--compact .asset-modal__column {
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
  }
  .asset-modal__column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    padding: 1.4rem 1.6rem;
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
  }
  .asset-modal__heading {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0;
  }
  .asset-option-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 360px;
    overflow-y: auto;
    padding-right: 0.4rem;
  }
  .asset-option-list::-webkit-scrollbar {
    width: 6px;
  }
  .asset-option-list::-webkit-scrollbar-thumb {
    background: #cbd5f5;
    border-radius: 999px;
  }
  .asset-option {
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: #fff;
    padding: 1rem 1.2rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
  }
  .asset-option.is-selected {
    border-color: #6366f1;
    box-shadow: 0 14px 36px rgba(99, 102, 241, 0.18);
  }
  .asset-option.is-disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  .asset-option__body {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .asset-option__thumb img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid #dbe1eb;
    background: #f8fafc;
  }
  .asset-option__details {
    flex: 1;
    min-width: 0;
  }
  .asset-option__title {
    font-weight: 600;
    color: #111827;
  }
  .asset-option__meta,
  .asset-option__condition {
    line-height: 1.3;
  }
  .asset-option__actions {
    margin-left: auto;
  }
  .asset-option__actions .btn {
    white-space: nowrap;
  }
  .asset-option.is-selected .asset-option__actions .btn {
    color: #fff;
    background-color: #4f46e5;
    border-color: #4f46e5;
  }
  .selected-assets {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .selected-asset {
    border: 1px dashed #cbd5f5;
    border-radius: 16px;
    padding: 1rem 1.1rem;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .selected-asset__top {
    display: flex;
    align-items: center;
    gap: 0.85rem;
  }
  .selected-asset__thumb img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: #fff;
  }
  .selected-asset__info {
    flex: 1;
  }
  .selected-asset__title {
    font-weight: 600;
  }
  .selected-asset__meta,
  .selected-asset__condition {
    font-size: 0.85rem;
    color: #6b7280;
  }
  .selected-asset__actions {
    margin-left: auto;
    display: flex;
    align-items: center;
  }
  .selected-asset__actions .btn {
    white-space: nowrap;
  }
  @media (max-width: 991.98px) {
    .asset-modal__column {
      padding: 1.1rem 1.2rem;
    }
  }
  @media (max-width: 575.98px) {
    .asset-option__body {
      flex-direction: column;
      align-items: flex-start;
    }
    .asset-option__actions {
      margin-left: 0;
      width: 100%;
      display: flex;
      justify-content: flex-end;
    }
    .selected-asset__top {
      align-items: flex-start;
      flex-direction: column;
      gap: 0.75rem;
    }
    .selected-asset__actions {
      width: 100%;
      margin-left: 0;
      justify-content: flex-end;
    }
  }
  .page-summary .card-body {
    padding: 1.75rem 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }
  .page-summary__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1.5rem;
  }
  .page-summary__identity {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    min-width: 0;
  }
  .page-summary__title {
    font-weight: 700;
    color: #1e1b4b;
  }
  .page-summary__meta {
    display: flex;
    flex-wrap: wrap;
    gap: .6rem 1rem;
    font-size: .9rem;
    color: #475569;
  }
  .page-summary__meta-item {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
  }
  .page-summary__meta-item .icon {
    color: #4f46e5;
  }
  .page-summary__actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .page-summary__actions .btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 12px;
    font-weight: 600;
    padding-inline: 1rem;
  }
  .page-summary__actions .btn .icon {
    width: 1.1rem;
    height: 1.1rem;
  }
  .page-summary__stats {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  }
  .stat-pill {
    background: linear-gradient(135deg, rgba(79, 70, 229, .08), rgba(14, 165, 233, .08));
    border: 1px solid rgba(148, 163, 184, .4);
    border-radius: 18px;
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .45rem;
  }
  .stat-pill__label {
    font-size: .75rem;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6366f1;
    font-weight: 700;
  }
  .stat-pill__value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
  }
  .stat-pill__hint {
    font-size: .85rem;
    color: #475569;
  }
  .room-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
  }
  .room-avatar {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(79, 70, 229, .16), rgba(14, 165, 233, .18));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .room-avatar__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .room-avatar__placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #4338ca;
    font-size: 1.85rem;
  }
  .room-avatar__icon {
    width: 36px;
    height: 36px;
    display: block;
  }
  .icon {
    width: 1.25rem;
    height: 1.25rem;
    display: inline-block;
    vertical-align: middle;
    line-height: 1;
    flex-shrink: 0;
  }
  .icon--sm {
    width: 1rem;
    height: 1rem;
  }
  .icon--md {
    width: 1.25rem;
    height: 1.25rem;
  }
  .icon--lg {
    width: 1.5rem;
    height: 1.5rem;
  }
  .icon--xl {
    width: 2.25rem;
    height: 2.25rem;
  }
  .icon--chevron {
    width: 1rem;
    height: 1rem;
  }
  .asset-table__image {
    width: 72px;
  }
  .asset-thumb {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    overflow: hidden;
    background: #f1f5f9;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 0 1px rgba(148, 163, 184, .18);
  }
  .asset-thumb__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }
  .asset-actions {
    display: flex;
    justify-content: flex-end;
  }
  .asset-actions form {
    margin: 0;
  }
  .asset-actions__btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 10px;
    font-weight: 600;
    padding-inline: .85rem;
  }
  .asset-actions__btn svg {
    width: 1rem;
    height: 1rem;
  }
  @media (max-width: 576px) {
    .room-avatar {
      width: 52px;
      height: 52px;
      border-radius: 16px;
    }
    .room-header {
      gap: .85rem;
    }
  }
  .badge-soft-primary {
    background: rgba(78, 84, 200, .12);
    color: #4e54c8;
    border: 1px solid rgba(78, 84, 200, .2);
    font-weight: 600;
    border-radius: 999px;
    padding: .35rem .75rem;
  }
  .badge-soft-teal {
    background: rgba(14, 165, 233, .12);
    color: #0ea5e9;
    border: 1px solid rgba(14, 165, 233, .2);
    font-weight: 600;
    border-radius: 999px;
    padding: .35rem .75rem;
  }
  .badge-soft-gray {
    background: rgba(148, 163, 184, .16);
    color: #475569;
    border-radius: 999px;
    padding: .25rem .65rem;
  }
  .asset-card {
    border-radius: 18px;
    overflow: hidden;
  }
  .asset-card .card-header {
    padding: 1.35rem 1.5rem;
  }
  .asset-card__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.25rem;
    flex-wrap: wrap;
  }
  .asset-card__collapse-trigger {
    flex: 1 1 260px;
    text-align: left;
    display: flex;
    align-items: flex-start;
  }
  .asset-card__collapse-trigger .toggle-content {
    width: 100%;
  }
  .asset-card__actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: .5rem;
    flex-wrap: wrap;
  }
  .asset-card__actions .btn {
    border-radius: 10px;
    font-weight: 600;
    padding-inline: 1rem;
    white-space: nowrap;
  }
  .asset-card .card-header .toggle-content {
    align-items: flex-start;
  }
  .asset-card__body {
    padding: 1.5rem;
  }
  .asset-card__body--flush {
    padding: 0;
  }
  .asset-filter {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    margin-bottom: 1rem;
  }
  .asset-filter__menu {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    overflow-x: auto;
    padding-bottom: .25rem;
  }
  .asset-filter__menu::-webkit-scrollbar {
    height: 4px;
  }
  .asset-filter__menu::-webkit-scrollbar-thumb {
    background: rgba(79, 70, 229, .3);
    border-radius: 999px;
  }
  .asset-filter__pill {
    border: 1px solid rgba(79, 70, 229, .18);
    background: #fff;
    color: #4f46e5;
    border-radius: 999px;
    padding: .35rem .85rem;
    font-size: .85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    cursor: pointer;
    transition: all .2s ease;
    white-space: nowrap;
  }
  .asset-filter__pill .badge {
    background: rgba(79, 70, 229, .12);
    color: #4f46e5;
    border-radius: 999px;
    padding: .1rem .45rem;
    font-weight: 600;
  }
  .asset-filter__pill:hover {
    background: rgba(79, 70, 229, .08);
  }
  .asset-filter__pill.active {
    background: #4f46e5;
    color: #fff;
    border-color: #4f46e5;
    box-shadow: 0 6px 12px rgba(79, 70, 229, .24);
  }
  .asset-filter__pill.active .badge {
    background: rgba(255, 255, 255, .25);
    color: #fff;
  }
  .asset-filter__summary {
    font-size: .85rem;
    color: #64748b;
  }
  .section-heading {
    display: flex;
    flex-direction: column;
    gap: .35rem;
  }
  .section-heading__title {
    font-weight: 700;
    color: #1e1b4b;
  }
  .section-heading__title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: rgba(79, 70, 229, .1);
    color: #4f46e5;
    margin-right: .75rem;
  }
  .section-heading__meta {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem .95rem;
    font-size: .85rem;
    color: #475569;
  }
  .section-heading__meta-item {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
  }
  .section-heading__meta-item .icon {
    color: #4f46e5;
  }
  .section-heading__subtitle {
    font-weight: 600;
    color: #0f172a;
  }
  .section-heading__desc {
    color: #6b7280;
    font-size: .875rem;
  }
  .card-toggle {
    display: block;
    width: 100%;
    padding: 0;
    background: none;
    border: none;
    text-align: left;
    cursor: pointer;
  }
  .card-toggle.asset-card__collapse-trigger {
    display: flex;
    align-items: center;
  }
  @media (max-width: 576px) {
    .asset-card__header {
      flex-direction: column;
      align-items: stretch;
    }
    .asset-card__actions {
      justify-content: flex-start;
    }
  }
  .card-toggle:focus {
    outline: none;
  }
  .toggle-content {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: .75rem 1.25rem;
    width: 100%;
  }
  .toggle-content .section-heading {
    flex: 1 1 260px;
    min-width: 0;
  }
  .toggle-meta {
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    margin-left: auto;
    flex: 0 0 auto;
  }
  @media (max-width: 767.98px) {
    .toggle-content {
      flex-direction: column;
      align-items: stretch;
    }
    .toggle-meta {
      margin-left: 0;
      justify-content: space-between;
      width: 100%;
    }
  }
  .toggle-icon {
    transition: transform .2s ease;
  }
  [data-bs-toggle="collapse"][aria-expanded="false"] .toggle-icon,
  [data-toggle="collapse"][aria-expanded="false"] .toggle-icon {
    transform: rotate(-90deg);
  }
  .empty-state {
    padding: 2rem 1rem;
    text-align: center;
    color: #6b7280;
  }
  .empty-state-min {
    text-align: center;
    color: #94a3b8;
    padding: .75rem 0;
    font-size: .875rem;
  }
  .slot-grid {
    display: grid;
    gap: 1.25rem;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  }
  .asset-preview {
    display: flex;
    flex-direction: column;
    gap: .5rem;
  }
  .asset-preview__frame {
    border: 1px dashed rgba(148, 163, 184, .6);
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    width: 100%;
    aspect-ratio: 4 / 3;
    transition: border-color .2s ease, background .2s ease;
  }
  .asset-preview__frame.has-image {
    border-style: solid;
    border-color: rgba(79, 70, 229, .25);
    background: #fff;
    padding: 0;
  }
  .asset-preview__frame img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 10px;
  }
  .asset-preview__frame.has-image img {
    object-fit: cover;
  }
  .slot-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid rgba(226, 232, 240, .8);
    box-shadow: 0 10px 25px rgba(15, 23, 42, .06);
    padding: 1.25rem;
  }
  .slot-card__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: .75rem;
    margin-bottom: 1rem;
  }
  .slot-code {
    font-weight: 700;
    font-size: 1.05rem;
    color: #4e54c8;
  }
  .slot-meta {
    display: flex;
    gap: .55rem;
    font-size: .92rem;
    color: #4b5563;
  }
  .slot-meta svg {
    flex: 0 0 auto;
    color: #6366f1;
  }
  .slot-meta--student {
    align-items: flex-start;
  }
  .slot-meta--empty {
    align-items: center;
  }
  .slot-holder {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    line-height: 1.1;
  }
  .slot-holder__name {
    font-weight: 600;
    color: #111827;
  }
  .slot-holder__id {
    font-size: .8rem;
    color: #6b7280;
  }
  .slot-assets-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: .9rem;
  }
  .slot-assets-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px dashed rgba(226, 232, 240, 1);
    padding-bottom: .75rem;
  }
  .slot-assets-list li:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .slot-asset__content {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    flex: 1;
  }
  .slot-asset__thumb {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid rgba(226, 232, 240, .9);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #94a3b8;
  }
  .slot-asset__thumb.has-image {
    background: #fff;
  }
  .slot-asset__thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .slot-asset__thumb-placeholder svg {
    width: 28px;
    height: 28px;
  }
  .slot-asset__details {
    display: flex;
    flex-direction: column;
  }
  .slot-asset-name {
    font-weight: 600;
  }
  .slot-asset-code {
    margin-top: .2rem;
  }
  .slot-asset-meta {
    margin-top: .25rem;
  }
  @media (max-width: 576px) {
    .slot-card {
      padding: 1rem;
    }
    .slot-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const autoHideAlerts = document.querySelectorAll('.alert[data-autohide="true"]');
    autoHideAlerts.forEach((alertEl) => {
      const timeout = parseInt(alertEl.dataset.autohideTimeout || '4000', 10);
      window.setTimeout(() => {
        if (window.bootstrap && window.bootstrap.Alert && typeof window.bootstrap.Alert.getOrCreateInstance === 'function') {
          window.bootstrap.Alert.getOrCreateInstance(alertEl).close();
        } else {
          alertEl.classList.remove('show');
          alertEl.addEventListener('transitionend', () => alertEl.remove(), { once: true });
          if (!alertEl.classList.contains('fade')) {
            alertEl.remove();
          }
        }
      }, timeout);
    });

    const initAssetPicker = (modalId, {
      inputName = 'assets',
      slotSelectSelector = null,
      requireSlotSelection = false,
    } = {}) => {
      const modalEl = document.getElementById(modalId);
      if (!modalEl) {
        return;
      }

      const assetList = modalEl.querySelector('[data-role="asset-picker"]');
      const selectedList = modalEl.querySelector('[data-role="selected-assets"]');
      const submitBtn = modalEl.querySelector('[data-role="asset-submit"]');
      const emptyMessage = selectedList ? selectedList.querySelector('[data-role="empty-state"]') : null;
      const slotSelect = slotSelectSelector ? modalEl.querySelector(slotSelectSelector) : null;
      const searchInput = modalEl.querySelector('[data-role="asset-search"]');
      const searchEmptyState = modalEl.querySelector('[data-role="asset-search-empty"]');

      if (!assetList || !selectedList || !submitBtn) {
        return;
      }

      const selection = new Map();

      const updateEmptyState = () => {
        if (emptyMessage) {
          emptyMessage.classList.toggle('d-none', selection.size > 0);
        }
      };

      const updateSubmitState = () => {
        const hasSelection = selection.size > 0;
        const slotReady = !requireSlotSelection || (slotSelect && slotSelect.value);
        submitBtn.disabled = !(hasSelection && slotReady);
      };

      if (slotSelect) {
        slotSelect.addEventListener('change', updateSubmitState);
      }

      const applySearchFilter = () => {
        if (!searchInput) {
          if (searchEmptyState) {
            searchEmptyState.classList.add('d-none');
          }
          return;
        }

        const keyword = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        assetList.querySelectorAll('[data-role="asset-item"]').forEach((optionEl) => {
          const name = (optionEl.dataset.name || '').toLowerCase();
          const code = (optionEl.dataset.code || '').toLowerCase();
          const condition = (optionEl.dataset.condition || '').toLowerCase();
          const matches = keyword === ''
            || name.includes(keyword)
            || code.includes(keyword)
            || condition.includes(keyword);

          optionEl.classList.toggle('d-none', !matches);

          if (matches) {
            visibleCount += 1;
          }
        });

        if (searchEmptyState) {
          searchEmptyState.classList.toggle('d-none', visibleCount !== 0);
        }
      };

      if (searchInput) {
        searchInput.addEventListener('input', applySearchFilter);
      }

      const setButtonState = (optionEl, isSelected) => {
        optionEl.classList.toggle('is-selected', isSelected);
        const toggleBtn = optionEl.querySelector('[data-role="asset-toggle"]');
        if (toggleBtn) {
          toggleBtn.textContent = isSelected ? 'Đã chọn' : 'Chọn';
          if (isSelected) {
            toggleBtn.classList.remove('btn-outline-primary');
            toggleBtn.classList.add('btn-primary');
          } else {
            toggleBtn.classList.add('btn-outline-primary');
            toggleBtn.classList.remove('btn-primary');
          }
        }
      };

      const removeSelectedItem = (id) => {
        const entry = selection.get(id);
        if (entry && entry.wrapper && entry.wrapper.parentNode) {
          entry.wrapper.parentNode.removeChild(entry.wrapper);
        }
        if (entry) {
          selection.delete(id);
        }
      };

      const createSelectedItem = (optionEl) => {
        const id = optionEl.dataset.id;
        const stock = parseInt(optionEl.dataset.stock || '0', 10);
        const image = optionEl.dataset.image || '';
        const name = optionEl.dataset.name || 'Không xác định';
        const code = optionEl.dataset.code || 'N/A';
        const condition = optionEl.dataset.condition || 'Không rõ';

        const wrapper = document.createElement('div');
        wrapper.className = 'selected-asset';
        wrapper.dataset.id = id;

        const top = document.createElement('div');
        top.className = 'selected-asset__top';

        const thumb = document.createElement('div');
        thumb.className = 'selected-asset__thumb';
        const img = document.createElement('img');
        img.src = image;
        img.alt = name;
        thumb.appendChild(img);

        const info = document.createElement('div');
        info.className = 'selected-asset__info';

        const title = document.createElement('div');
        title.className = 'selected-asset__title';
        title.textContent = name;

        const meta = document.createElement('div');
        meta.className = 'selected-asset__meta';
        meta.textContent = `Mã: ${code} · Tồn kho: ${stock}`;

        const conditionEl = document.createElement('div');
        conditionEl.className = 'selected-asset__condition';
        conditionEl.textContent = `Tình trạng: ${condition}`;

        info.appendChild(title);
        info.appendChild(meta);
        info.appendChild(conditionEl);

        top.appendChild(thumb);
        top.appendChild(info);

        const actions = document.createElement('div');
        actions.className = 'selected-asset__actions';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-outline-secondary btn-sm';
        removeBtn.textContent = 'Bỏ chọn';
        removeBtn.addEventListener('click', () => {
          removeSelectedItem(id);
          setButtonState(optionEl, false);
          updateEmptyState();
          updateSubmitState();
        });

        actions.appendChild(removeBtn);
        top.appendChild(actions);

        wrapper.appendChild(top);

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `${inputName}[${id}]`;
        hiddenInput.value = '1';
        wrapper.appendChild(hiddenInput);

        selectedList.appendChild(wrapper);

        selection.set(id, {
          wrapper,
          input: hiddenInput,
          option: optionEl,
        });

        optionEl.dataset.oldQty = '';
      };

      const toggleSelection = (optionEl, forceSelect = null) => {
        const id = optionEl.dataset.id;
        const alreadySelected = selection.has(id);
        const shouldSelect = typeof forceSelect === 'boolean' ? forceSelect : !alreadySelected;
        const stock = parseInt(optionEl.dataset.stock || '0', 10);

        if (shouldSelect) {
          if (stock <= 0 || alreadySelected) {
            return;
          }
          createSelectedItem(optionEl);
          setButtonState(optionEl, true);
        } else {
          if (!alreadySelected) {
            return;
          }
          removeSelectedItem(id);
          setButtonState(optionEl, false);
        }

        updateEmptyState();
        updateSubmitState();
      };

      assetList.querySelectorAll('[data-role="asset-item"]').forEach((optionEl) => {
        const toggleBtn = optionEl.querySelector('[data-role="asset-toggle"]');
        const stock = parseInt(optionEl.dataset.stock || '0', 10);

        if (stock <= 0) {
          optionEl.classList.add('is-disabled');
          if (toggleBtn) {
            toggleBtn.textContent = 'Hết hàng';
            toggleBtn.disabled = true;
            toggleBtn.classList.remove('btn-outline-primary');
            toggleBtn.classList.add('btn-secondary');
          }
          return;
        }

        if (toggleBtn) {
          toggleBtn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleSelection(optionEl);
          });
        }

        optionEl.addEventListener('click', (event) => {
          if (event.target.closest('[data-role="asset-toggle"]')) {
            return;
          }
          toggleSelection(optionEl);
        });

        const oldQty = optionEl.dataset.oldQty;
        if (oldQty) {
          toggleSelection(optionEl, true);
        }
      });

      modalEl.addEventListener('shown.bs.modal', () => {
        if (searchInput) {
          searchInput.value = '';
          applySearchFilter();
        } else if (searchEmptyState) {
          searchEmptyState.classList.add('d-none');
        }

        updateSubmitState();
      });
      updateEmptyState();
      updateSubmitState();
      applySearchFilter();
    };

    const setupRoomAssetFilter = () => {
      const container = document.querySelector('[data-role="room-asset-filter"]');
      if (!container) {
        return;
      }

      const buttons = Array.from(container.querySelectorAll('[data-filter]'));
      if (!buttons.length) {
        return;
      }

      const tableBody = document.getElementById('roomAssetsTableBody');
      if (!tableBody) {
        return;
      }

      const rows = Array.from(tableBody.querySelectorAll('[data-filter-key]'));
      const summaryEl = container.querySelector('[data-filter-summary]');

      const reorderRows = () => {
        let index = 1;
        rows.forEach((row) => {
          if (row.classList.contains('d-none')) {
            return;
          }
          const orderCell = row.querySelector('.asset-order');
          if (orderCell) {
            orderCell.textContent = index;
          }
          index += 1;
        });
      };

      const calculateVisibleTotals = () => {
        return rows.reduce((acc, row) => {
          if (!row.classList.contains('d-none')) {
            acc.count += 1;
            acc.quantity += Number(row.dataset.rowQuantity || 0);
          }
          return acc;
        }, { count: 0, quantity: 0 });
      };

      const updateSummary = (button) => {
        if (!summaryEl) {
          return;
        }
        const totals = calculateVisibleTotals();
        const label = button.dataset.label || '';
        if (button.dataset.filter === 'all') {
          summaryEl.textContent = `Đang hiển thị ${totals.count} dòng · Tổng số lượng: ${totals.quantity}`;
        } else {
          summaryEl.textContent = `${label}: ${totals.count} dòng · Tổng số lượng: ${totals.quantity}`;
        }
      };

      const applyFilter = (button) => {
        const filterValue = button.dataset.filter;
        rows.forEach((row) => {
          const match = filterValue === 'all' || row.dataset.filterKey === filterValue;
          row.classList.toggle('d-none', !match);
        });
        reorderRows();
        updateSummary(button);
      };

      buttons.forEach((button) => {
        button.addEventListener('click', () => {
          if (button.classList.contains('active')) {
            return;
          }
          buttons.forEach((btn) => btn.classList.remove('active'));
          button.classList.add('active');
          applyFilter(button);
        });
      });

      const initialButton = buttons.find((btn) => btn.classList.contains('active')) || buttons[0];
      if (initialButton) {
        applyFilter(initialButton);
      }
    };

    setupRoomAssetFilter();

    initAssetPicker('modalAddRoomAsset', {
      inputName: 'assets',
    });

    initAssetPicker('modalAddSlotAsset', {
      inputName: 'assets',
      slotSelectSelector: '[data-role="slot-select"]',
      requireSlotSelection: true,
    });

    // Xóa tài sản khỏi slot (detach pivot)
    document.body.addEventListener('click', async (event) => {
      const btn = event.target.closest('[data-role="remove-slot-asset"]');
      if (!btn) {
        return;
      }
      event.preventDefault();
      const confirmed = window.confirm('Bạn có chắc muốn xóa tài sản này khỏi slot?');
      if (!confirmed) {
        return;
      }
      const url = btn.dataset.url;
      const assetId = btn.dataset.assetId;
      const formData = new FormData();
      formData.append('tai_san_id', assetId);
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: formData,
        });
        if (!res.ok) {
          throw new Error('Request failed');
        }
        // Tải lại để cập nhật số lượng & thống kê
        window.location.reload();
      } catch (e) {
        (window.showToast || window.alert)('Không thể xóa tài sản khỏi slot.');
      }
    });
  });
</script>
@endpush

