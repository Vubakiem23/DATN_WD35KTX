@extends('admin.layouts.admin')
@section('title','Quản lý khu')

@section('content')
    <div class="container-fluid khu-management-page">
        @if(session('status'))
            @push('scripts')
                <script>
                    window.addEventListener('DOMContentLoaded', function () {
                        (window.showToast || window.alert)(@json(session('status')), 'success');
                    });
                </script>
            @endpush
            <noscript>
                <div class="alert alert-success">{{ session('status') }}</div>
            </noscript>
        @endif
        @if(session('error'))
            @push('scripts')
                <script>
                    window.addEventListener('DOMContentLoaded', function () {
                        (window.showToast || window.alert)(@json(strip_tags(session('error'))), 'error');
                    });
                </script>
            @endpush
            <noscript>
                <div class="alert alert-danger">{!! session('error') !!}</div>
            </noscript>
        @endif

        @if(($needsMigration ?? false) === true)
            <div class="alert alert-warning shadow-sm">
                <h5 class="mb-1">Thiếu bảng dữ liệu khu</h5>
                <p class="mb-0 small">Hệ thống chưa có bảng <code>khu</code>. Vui lòng chạy migrate trước khi quản lý dữ liệu.</p>
            </div>
        @else
            @php
                $khus->loadCount('phongs');
                $totalKhu = $khus->count();
                $maleCount = $khus->where('gioi_tinh', 'Nam')->count();
                $femaleCount = $khus->where('gioi_tinh', 'Nữ')->count();
                $otherCount = max(0, $totalKhu - $maleCount - $femaleCount);
                $groupedByGender = $khus->groupBy(function ($item) {
                    return $item->gioi_tinh ?? 'Khác';
                });
                $panes = collect();
                $panes->put('all', ['label' => 'Tất cả', 'items' => $khus, 'badge' => $totalKhu]);
                $index = 0;
                foreach ($groupedByGender as $gender => $items) {
                    $slug = \Illuminate\Support\Str::slug($gender ?: 'khac');
                    if ($slug === '') {
                        $slug = 'gender-' . $index;
                    }
                    if ($panes->has($slug)) {
                        $slug .= '-' . $index;
                    }
                    $panes->put($slug, [
                        'label' => $gender ?: 'Khác',
                        'items' => $items,
                        'badge' => $items->count(),
                    ]);
                    $index++;
                }
            @endphp

                <div>
                    <h3 class="khu-page__title mb-1">Khu ký túc xá</h3>
                    <p class="text-muted mb-0">Theo dõi và tổ chức các khu theo giới tính và số lượng phòng.</p>
                </div>
              

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="khu-stat-card">
                        <span class="khu-stat-card__label">Tổng khu</span>
                        <span class="khu-stat-card__value">{{ $totalKhu }}</span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="khu-stat-card khu-stat-card--male">
                        <span class="khu-stat-card__label">Khu Nam</span>
                        <span class="khu-stat-card__value">{{ $maleCount }}</span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="khu-stat-card khu-stat-card--female">
                        <span class="khu-stat-card__label">Khu Nữ</span>
                        <span class="khu-stat-card__value">{{ $femaleCount }}</span>
                    </div>
                </div>
                @if($otherCount > 0)
                    <div class="col-md-4 col-sm-6">
                        <div class="khu-stat-card khu-stat-card--other">
                            <span class="khu-stat-card__label">Khác</span>
                            <span class="khu-stat-card__value">{{ $otherCount }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <h3 class="khu-page__title mb-1"><i class="fa fa-building me-2"></i> Danh sách các khu</h3>
            <p class="text-muted mb-0">Theo dõi và tổ chức các khu theo từng nhóm.</p>
            <div class="d-flex gap-2 mb-3">
                <a href="{{ route('khu.create') }}" class="btn btn-dergin btn-dergin--info">
                    <i class="fa fa-plus"></i><span>Tạo khu</span>
                </a>
            </div>

            <div class="khu-toolbar mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-sm-6 col-lg-4">
                        <input type="text" id="khuFilterName" class="form-control" placeholder="Tìm theo tên khu hoặc mô tả...">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <select id="khuFilterGender" class="form-select">
                            <option value="all">Tất cả giới tính</option>
                            @foreach($groupedByGender->keys() as $gender)
                                @php $value = \Illuminate\Support\Str::lower($gender ?: 'khác'); @endphp
                                <option value="{{ $value }}">{{ $gender ?: 'Khác' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <ul class="nav nav-tabs mb-3" id="khuGenderTabs" role="tablist">
                @foreach($panes as $key => $pane)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}"
                                data-bs-toggle="tab" data-bs-target="#pane-{{ $key }}"
                                data-toggle="tab" data-target="#pane-{{ $key }}"
                                type="button" role="tab">
                            {{ $pane['label'] }}
                            <span class="badge rounded-pill ms-1 khu-tab-badge">{{ $pane['badge'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($panes as $key => $pane)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }} khu-tab-pane" id="pane-{{ $key }}" role="tabpanel">
                        @if($pane['items']->isEmpty())
                            <div class="khu-empty-state text-center text-muted py-4">Chưa có khu trong nhóm này.</div>
                        @else
                            <div class="khu-table-wrapper">
                                <div class="table-responsive">
                                    <table class="table align-middle khu-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th>Tên khu</th>
                                                <th>Giới tính</th>
                                                <th>Gía Theo Sinh Viên/Tháng</th>
                                                <th>Số phòng</th>
                                                <th>Mô tả</th>
                                                <th class="text-end">Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pane['items'] as $khu)
                                                @php
                                                    $gender = \Illuminate\Support\Str::lower($khu->gioi_tinh ?? 'khác');
                                                    $badgeClass = match ($khu->gioi_tinh) {
                                                        'Nam' => 'bg-primary-soft text-primary',
                                                        'Nữ' => 'bg-pink-soft text-danger',
                                                        default => 'bg-secondary-soft text-secondary'
                                                    };
                                                @endphp
                                                <tr class="khu-row" data-name="{{ \Illuminate\Support\Str::lower($khu->ten_khu ?? '') }}"
                                                    data-description="{{ \Illuminate\Support\Str::lower($khu->mo_ta ?? '') }}"
                                                    data-gender="{{ $gender }}">
                                                    <td class="text-center text-muted" data-label="#">{{ $loop->iteration }}</td>
                                                    <td data-label="Tên khu">
                                                        <div class="khu-meta">
                                                            <div class="khu-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($khu->ten_khu ?? 'K', 0, 1)) }}</div>
                                                            <div class="khu-meta__info">
                                                                <div class="khu-meta__title">{{ $khu->ten_khu }}</div>
                                                                <div class="khu-meta__subtitle text-muted">Tạo: {{ optional($khu->created_at)->format('d/m/Y') ?? 'Không rõ' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td data-label="Giới tính">
                                                        <span class="badge khu-badge {{ $badgeClass }}">{{ $khu->gioi_tinh ?? 'Không xác định' }}</span>
                                                    </td>
                                                    <td data-label="Giá mỗi slot">
                                                        @php
                                                            $pricePerSlot = $khu->gia_moi_slot ?? null;
                                                        @endphp
                                                        @if(!is_null($pricePerSlot))
                                                            <span class="fw-semibold">{{ number_format((int)$pricePerSlot, 0, ',', '.') }}&nbsp;VND/tháng</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td data-label="Số phòng">
                                                        <strong>{{ $khu->phongs_count ?? 0 }}</strong>
                                                    </td>
                                                    <td data-label="Mô tả">
                                                        {{ $khu->mo_ta ? \Illuminate\Support\Str::limit($khu->mo_ta, 80) : '—' }}
                                                    </td>
                                                    <td class="text-end" data-label="Hành động">
                                                        <div class="khu-actions">
                                                            <a href="{{ route('khu.show', $khu) }}" class="btn btn-dergin btn-dergin--muted" title="Xem chi tiết">
                                                                <i class="fa fa-eye"></i><span>Chi tiết</span>
                                                            </a>
                                                            <a href="{{ route('khu.edit', $khu) }}" class="btn btn-dergin btn-dergin--info" title="Sửa khu">
                                                                <i class="fa fa-pencil"></i><span>Sửa</span>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="khu-empty-state text-center text-muted py-4 d-none">Không có khu phù hợp.</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @push('styles')
                <style>
                    .khu-management-page{padding-bottom:2rem;}
                    .khu-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
                    .khu-stat-card{background:linear-gradient(135deg,#f3f4f6 0%,#ffffff 100%);border-radius:16px;padding:1.25rem 1.5rem;box-shadow:0 12px 30px rgba(15,23,42,0.08);display:flex;flex-direction:column;gap:.35rem}
                    .khu-stat-card__label{text-transform:uppercase;font-size:.75rem;letter-spacing:.08em;color:#6b7280}
                    .khu-stat-card__value{font-size:1.75rem;font-weight:700;color:#111827}
                    .khu-stat-card--male{background:linear-gradient(135deg,rgba(59,130,246,.15) 0%,#ffffff 100%)}
                    .khu-stat-card--female{background:linear-gradient(135deg,rgba(236,72,153,.15) 0%,#ffffff 100%)}
                    .khu-stat-card--other{background:linear-gradient(135deg,rgba(107,114,128,.15) 0%,#ffffff 100%)}
                    .khu-toolbar{background:#ffffff;border-radius:14px;padding:1rem 1.25rem;box-shadow:0 10px 24px rgba(15,23,42,0.06)}
                    .khu-table-wrapper{background:#fff;border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,0.08);padding:1.5rem}
                    .khu-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
                    .khu-table thead th{border:none;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;padding-bottom:.75rem}
                    .khu-table tbody tr{background:#f8fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
                    .khu-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(15,23,42,0.12)}
                    .khu-table tbody td{border:none;vertical-align:middle;padding:1rem 1rem}
                    .khu-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
                    .khu-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
                    .khu-meta{display:flex;align-items:center;gap:.9rem}
                    .khu-avatar{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:#fff;box-shadow:0 10px 20px rgba(99,102,241,.25)}
                    .khu-meta__title{font-weight:600;font-size:1rem;color:#111827;margin-bottom:.125rem}
                    .khu-meta__subtitle{font-size:.78rem}
                    .khu-badge{font-size:.75rem;padding:.35rem .7rem;border-radius:999px}
                    .bg-primary-soft{background:rgba(59,130,246,.12)!important}
                    .bg-pink-soft{background:rgba(236,72,153,.12)!important}
                    .bg-secondary-soft{background:rgba(107,114,128,.15)!important}
                    .khu-actions{display:flex;justify-content:flex-end;gap:.5rem}
                    .btn-dergin{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:999px;font-weight:600;font-size:.78rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 8px 20px rgba(78,84,200,.25);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none}
                    .btn-dergin span{line-height:1}
                    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(78,84,200,.35);color:#fff}
                    .btn-dergin i{font-size:.85rem}
                    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
                    .btn-dergin--info{background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%)}
                    .khu-tab-badge{background:rgba(99,102,241,.12);color:#4f46e5;font-size:.65rem}
                    .khu-empty-state{font-size:.88rem}
                    @media (max-width:992px){
                        .khu-table thead{display:none}
                        .khu-table tbody{display:block}
                        .khu-table tbody tr{display:flex;flex-direction:column;padding:1.1rem}
                        .khu-table tbody td{display:flex;justify-content:space-between;padding:.45rem 0}
                        .khu-table tbody td[data-label]{position:relative;padding-left:130px}
                        .khu-table tbody td[data-label]::before{content:attr(data-label);position:absolute;left:0;font-weight:600;color:#6b7280;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}
                        .khu-actions{justify-content:flex-start}
                    }
                </style>
            @endpush

            @push('scripts')
                <script>
                    (function(){
                        function getActivePane(){
                            return document.querySelector('.khu-tab-pane.show.active') || document.querySelector('.khu-tab-pane');
                        }
                        function applyKhuFilter(){
                            const pane = getActivePane();
                            if(!pane) return;
                            const term = (document.getElementById('khuFilterName')?.value || '').toLowerCase();
                            const genderValue = (document.getElementById('khuFilterGender')?.value || 'all');
                            const rows = pane.querySelectorAll('.khu-row');
                            let visible = 0;
                            rows.forEach(function(row){
                                const name = (row.dataset.name || '');
                                const description = (row.dataset.description || '');
                                const gender = (row.dataset.gender || '');
                                const okTerm = !term || name.indexOf(term) !== -1 || description.indexOf(term) !== -1;
                                const okGender = genderValue === 'all' || gender === genderValue;
                                const shouldShow = okTerm && okGender;
                                row.style.display = shouldShow ? '' : 'none';
                                if(shouldShow) visible++;
                            });
                            const emptyEl = pane.querySelector('.khu-empty-state');
                            if(emptyEl){
                                emptyEl.classList.toggle('d-none', visible !== 0);
                                if(visible === 0){
                                    emptyEl.textContent = term || genderValue !== 'all'
                                        ? 'Không có khu phù hợp với bộ lọc.'
                                        : 'Chưa có khu trong nhóm này.';
                                } else {
                                    emptyEl.textContent = 'Không có khu phù hợp.';
                                }
                            }
                        }
                        const nameInput = document.getElementById('khuFilterName');
                        const genderSelect = document.getElementById('khuFilterGender');
                        if(nameInput){ nameInput.addEventListener('input', function(){ applyKhuFilter(); }); }
                        if(genderSelect){ genderSelect.addEventListener('change', function(){ applyKhuFilter(); }); }
                        document.querySelectorAll('#khuGenderTabs button').forEach(function(btn){
                            btn.addEventListener('click', function(){
                                setTimeout(function(){ applyKhuFilter(); }, 0);
                            });
                        });
                        if(document.readyState === 'loading'){
                            document.addEventListener('DOMContentLoaded', applyKhuFilter);
                        } else {
                            applyKhuFilter();
                        }
                    })();
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('#khuGenderTabs button').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                var target = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
                                if (!target) return;
                                document.querySelectorAll('.khu-tab-pane').forEach(function (p) {
                                    p.classList.remove('show', 'active');
                                });
                                var pane = document.querySelector(target);
                                if (pane) {
                                    pane.classList.add('show', 'active');
                                }
                                document.querySelectorAll('#khuGenderTabs button').forEach(function (b) {
                                    b.classList.remove('active');
                                });
                                btn.classList.add('active');
                            });
                        });
                    });
                </script>
            @endpush
        @endif
    </div>
@endsection
