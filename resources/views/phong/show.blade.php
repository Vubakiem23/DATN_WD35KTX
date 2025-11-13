@extends('admin.layouts.admin')

@section('title','Chi tiết phòng')

@section('content')
<div class="container-fluid khu-detail-page">
  <div class="align-items-center mb-3">
    <div>
      <h3 class="khu-page__title mb-1">Chi Tiết Phòng: {{ $phong->ten_phong }}</h3>
      <p class="text-muted mb-0">Theo dõi và tổ chức Chi Tiết Phòng</p>
    </div>
    <div>
      <a href="{{ route('phong.index') }}" class="btn btn-dergin btn-dergin--muted" title="Quay lại"><i class="fa fa-arrow-left"></i><span>Về danh sách</span></a>
    </div>
  </div>
  @push('styles')
  <style>
    /* Trang trí riêng cho trang chi tiết phòng */
    .khu-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
    .khu-detail-page{padding-bottom:2rem}

    /* Buttons + shared styles (match Khu detail) */
    .btn-dergin{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:999px;font-weight:600;font-size:.78rem;border:none;color:#fff;background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%);box-shadow:0 8px 20px rgba(79,70,229,.25);text-decoration:none}
    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(79,70,229,.35);color:#fff}
    .btn-dergin i{font-size:.85rem}
    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
    .btn-dergin--info{background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%)}
    .btn-dergin--danger{background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%)}

    /* Toolbar + table wrapper (match Khu detail) */
    .khu-toolbar{background:#ffffff;border-radius:14px;padding:1rem 1.25rem;box-shadow:0 10px 24px rgba(15,23,42,0.06)}
    .khu-rooms-wrapper{background:#fff;border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,0.08);padding:1.25rem}
    .khu-rooms-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
    .khu-rooms-table thead th{border:none;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;padding-bottom:.75rem}
    .khu-rooms-table tbody tr{background:#f8fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
    .khu-rooms-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(15,23,42,0.12)}
    .khu-rooms-table tbody td{border:none;vertical-align:middle;padding:1rem 1rem}
    .khu-rooms-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
    .khu-rooms-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
    .slot-actions{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end}

    .room-detail-layout{display:flex;flex-direction:column;gap:1.5rem;margin:0;}
    .room-detail-layout__aside,
    .room-detail-layout__main{width:100%;}
    .room-detail-layout__aside{display:flex;flex-direction:column;gap:1.25rem;}
    .room-detail-layout__aside .card,
    .room-detail-layout__main > *{width:100%;}
    @media (min-width: 992px){
      .room-detail-layout{flex-direction:row;align-items:flex-start;}
      .room-detail-layout__aside{flex:0 0 360px;max-width:360px;}
      .room-detail-layout__main{flex:1 1 auto;}
    }

    .khu-rooms-wrapper{overflow-x:auto;}

    .room-cover{aspect-ratio: 4 / 3; width:100%; object-fit:cover; border-radius:.25rem;}
    .slot-thumb{width:110px;height:72px;object-fit:cover;border-radius:.35rem;}
    .table td, .table th{vertical-align: middle;}
    .slots-table{table-layout:fixed;width:100%;}
    .slots-table th,.slots-table td{white-space:normal;word-break:break-word;}
    .slots-table th:nth-child(1){width:16%}
    .slots-table th:nth-child(2){width:18%}
    .slots-table th:nth-child(3){width:24%}
    .slots-table th:nth-child(4){width:20%}
    .slots-table th:nth-child(5){width:14%}
    .slots-table th:nth-child(6){width:20%}
    .slot-actions{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-end}
    .slot-actions .btn{padding:.35rem .6rem;font-size:13px}
    .table td .text-trunc{display:inline-block;max-width:100%;white-space:normal;overflow:visible;text-overflow:clip;}
    #assignAssetsModal .modal-dialog{max-width:960px}
    #assignAssetsModal .modal-body{padding:1.75rem}
    #assignAssetsModal .asset-modal{display:flex;flex-direction:column;gap:1.5rem}
    #assignAssetsModal .asset-modal__column{display:flex;flex-direction:column;gap:1rem;background:#fff;border-radius:18px;border:1px solid #e5e7eb;padding:1.35rem 1.5rem;box-shadow:0 16px 36px rgba(15,23,42,.08)}
    #assignAssetsModal .asset-modal__heading{font-weight:600;font-size:1rem;margin-bottom:0;color:#1e1b4b}
    #assignAssetsModal .asset-modal__search .form-control{border-radius:12px;padding:.55rem .95rem;border:1px solid rgba(99,102,241,.2)}
    #assignAssetsModal .asset-modal__search .form-control:focus{border-color:#4f46e5;box-shadow:0 0 0 .2rem rgba(79,70,229,.15)}
    #assignAssetsModal .asset-option-list{display:flex;flex-direction:column;gap:1rem;max-height:360px;overflow-y:auto;padding-right:.4rem}
    #assignAssetsModal .asset-option-list::-webkit-scrollbar{width:6px}
    #assignAssetsModal .asset-option-list::-webkit-scrollbar-thumb{background:#cbd5f5;border-radius:999px}
    #assignAssetsModal .asset-option{border:1px solid #e5e7eb;border-radius:16px;padding:1rem 1.1rem;background:#fff;transition:box-shadow .2s ease,border-color .2s ease;cursor:pointer}
    #assignAssetsModal .asset-option:hover{box-shadow:0 14px 28px rgba(15,23,42,.1);border-color:#c7d2fe}
    #assignAssetsModal .asset-option.is-selected{border-color:#6366f1;box-shadow:0 18px 32px rgba(99,102,241,.22)}
    #assignAssetsModal .asset-option.is-disabled{opacity:.55;pointer-events:none}
    #assignAssetsModal .asset-option__body{display:flex;align-items:center;gap:1rem}
    #assignAssetsModal .asset-option__thumb{width:64px;height:64px;border-radius:16px;overflow:hidden;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:inset 0 0 0 1px rgba(148,163,184,.2)}
    #assignAssetsModal .asset-option__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .asset-option__details{flex:1;display:flex;flex-direction:column;gap:.35rem}
    #assignAssetsModal .asset-option__title{font-weight:600;color:#1e1b4b}
    #assignAssetsModal .asset-option__meta,
    #assignAssetsModal .asset-option__condition{font-size:.85rem;color:#64748b}
    #assignAssetsModal .asset-option__actions{display:flex;flex-direction:column;align-items:flex-end;gap:.35rem}
    #assignAssetsModal .asset-option__actions .btn{border-radius:10px;font-weight:600;padding:.35rem .85rem}
    #assignAssetsModal .selected-assets{display:flex;flex-direction:column;gap:.75rem}
    #assignAssetsModal .selected-asset{border:1px solid #dbeafe;border-radius:16px;padding:1rem 1.1rem;background:#f8fafc;display:flex;flex-direction:column;gap:.75rem;box-shadow:0 14px 28px rgba(15,23,42,.06)}
    #assignAssetsModal .selected-asset__top{display:flex;gap:1rem;align-items:center}
    #assignAssetsModal .selected-asset__thumb{width:56px;height:56px;border-radius:14px;overflow:hidden;flex-shrink:0;background:#fff;box-shadow:inset 0 0 0 1px rgba(148,163,184,.18)}
    #assignAssetsModal .selected-asset__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .selected-asset__info{flex:1;display:flex;flex-direction:column;gap:.35rem}
    #assignAssetsModal .selected-asset__title{font-weight:600;color:#1f2937}
    #assignAssetsModal .selected-asset__meta,
    #assignAssetsModal .selected-asset__condition{font-size:.85rem;color:#64748b}
    #assignAssetsModal .selected-asset__actions{margin-left:auto}
    #assignAssetsModal .selected-asset__actions .btn{border-radius:10px}
    #assignAssetsModal .asset-empty-message{padding:1.5rem 0;text-align:center;color:#94a3b8;font-size:.95rem}
    #assignAssetsModal .slot-current-assets{border:1px solid rgba(79,70,229,.18);border-radius:18px;padding:1rem 1.25rem;background:rgba(79,70,229,.05);display:flex;flex-direction:column;gap:.75rem}
    #assignAssetsModal .slot-current-assets__title{font-weight:600;color:#3730a3}
    #assignAssetsModal .slot-current-assets__list{display:flex;flex-direction:column;gap:.65rem}
    #assignAssetsModal .slot-current-asset{display:flex;gap:.75rem;align-items:center;background:#fff;border-radius:14px;padding:.55rem .8rem;border:1px solid #c7d2fe;box-shadow:0 8px 18px rgba(79,70,229,.15)}
    #assignAssetsModal .slot-current-asset__thumb{width:44px;height:44px;border-radius:12px;overflow:hidden;background:#eef2ff;flex-shrink:0}
    #assignAssetsModal .slot-current-asset__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .slot-current-asset__info{display:flex;flex-direction:column;gap:.2rem;font-size:.85rem}
    #assignAssetsModal .slot-current-asset__name{font-weight:600;color:#1e1b4b}
    #assignAssetsModal .slot-current-asset__meta{color:#64748b}
    #assignAssetsModal .slot-current-asset__badge{font-size:.7rem;border-radius:999px;padding:.15rem .5rem;background:#ede9fe;color:#5b21b6;margin-left:.35rem}
    #assignAssetsModal .slot-current-asset__actions{margin-left:auto;display:flex;align-items:center}
    #assignAssetsModal .slot-current-asset__actions .btn{border-radius:10px;display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .75rem}
    @media (max-width: 991.98px){#assignAssetsModal .asset-option__body{flex-direction:column;align-items:flex-start}#assignAssetsModal .asset-option__actions{width:100%;align-items:flex-end}}
    @media (max-width: 575.98px){#assignAssetsModal .modal-body{padding:1.25rem}#assignAssetsModal .asset-option__body{align-items:flex-start}#assignAssetsModal .selected-asset__top{flex-direction:column;align-items:flex-start}}
    #assignStudentModal .modal-dialog{max-width:520px}
    #assignStudentModal .form-group{display:flex;flex-direction:column;gap:.5rem}
    #assignStudentModal .form-group label{font-weight:600;color:#4338ca;margin-bottom:0;letter-spacing:.01em}
    #assignStudentModal .assign-student-select{padding:.65rem 1rem;border-radius:14px;border:1px solid rgba(99,102,241,.28);box-shadow:0 10px 28px rgba(79,70,229,.12);font-weight:600;color:#1e1b4b;transition:border-color .2s ease,box-shadow .2s ease;font-size:14px;line-height:1.5;min-height:calc(2.5rem + 2px)}
    #assignStudentModal .assign-student-select:focus{border-color:#4f46e5;box-shadow:0 0 0 .25rem rgba(79,70,229,.18);outline:none}
    #assignStudentModal .assign-student-select option{white-space:normal;line-height:1.5;padding:.35rem .5rem;font-weight:500}
    .room-info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.75rem;margin-bottom:1.5rem}
    .room-info-card{display:flex;align-items:flex-start;gap:.75rem;padding:.85rem 1rem;border-radius:18px;background:#f8fafc;border:1px solid rgba(148,163,184,.28);box-shadow:0 10px 24px rgba(15,23,42,.06);transition:transform .2s ease,box-shadow .2s ease}
    .room-info-card:hover{transform:translateY(-2px);box-shadow:0 16px 32px rgba(15,23,42,.09)}
    .room-info-card__icon{width:42px;height:42px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.05rem;flex-shrink:0;color:#4338ca;background:rgba(99,102,241,.18)}
    .room-info-card--zone .room-info-card__icon{color:#1d4ed8;background:rgba(59,130,246,.18)}
    .room-info-card--type .room-info-card__icon{color:#16a34a;background:rgba(34,197,94,.16)}
    .room-info-card--capacity .room-info-card__icon{color:#7c3aed;background:rgba(167,139,250,.2)}
    .room-info-card--price .room-info-card__icon{color:#dc2626;background:rgba(248,113,113,.2)}
    .room-info-card__label{font-size:.72rem;font-weight:600;color:#6b7280;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.15rem}
    .room-info-card__value{font-size:1rem;font-weight:600;color:#1f2937}
    .room-info-card__meta{margin-top:.4rem;display:flex;flex-wrap:wrap;gap:.4rem}
    .room-info-card__meta-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .65rem;border-radius:999px;font-size:.78rem;font-weight:600;background:rgba(244,114,182,.12);color:#be185d;border:1px solid rgba(244,114,182,.35)}
    .room-info-card__meta-badge i{font-size:.75rem}
    .room-occupancy{border-radius:18px;background:#f1f5f9;padding:1.1rem 1.25rem;border:1px solid rgba(148,163,184,.32);box-shadow:0 12px 28px rgba(15,23,42,.05);margin-bottom:1.25rem}
    .room-occupancy__label{font-size:.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem}
    .room-occupancy__value{font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:.5rem}
    .room-occupancy .progress{height:12px;border-radius:999px;background:#e2e8f0;overflow:hidden}
    .room-occupancy .progress-bar{border-radius:999px}
    .room-assets-card{border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,0.08);overflow:hidden}
    .room-assets-card .card-header{background:#fff;border-bottom:1px solid rgba(148,163,184,.25);padding:1.35rem 1.5rem}
    .room-assets-card .card-title{font-size:1.05rem;font-weight:600;color:#1f2937;margin-bottom:.15rem}
    .room-assets-card .card-subtitle{font-size:.85rem;color:#6b7280}
    .badge-soft-primary{background:rgba(78,84,200,.12);color:#4e54c8;border:1px solid rgba(78,84,200,.2);font-weight:600;border-radius:999px;padding:.35rem .75rem}
    .asset-card__header{display:flex;align-items:flex-start;justify-content:space-between;gap:1.25rem;flex-wrap:wrap}
    .card-toggle{display:block;width:100%;padding:0;background:none;border:none;text-align:left;cursor:pointer}
    .card-toggle:focus{outline:none}
    .asset-card__collapse-trigger{flex:1 1 260px;text-align:left;display:flex;align-items:flex-start}
    .toggle-content{display:flex;flex-wrap:wrap;align-items:flex-start;gap:.75rem 1.25rem;width:100%}
    .toggle-content .section-heading{flex:1 1 260px;min-width:0}
    .toggle-meta{display:inline-flex;align-items:center;gap:.75rem;margin-left:auto;flex:0 0 auto}
    .toggle-icon{transition:transform .2s ease}
    [data-bs-toggle="collapse"][aria-expanded="false"] .toggle-icon,
    [data-toggle="collapse"][aria-expanded="false"] .toggle-icon{transform:rotate(-90deg)}
    .section-heading{display:flex;flex-direction:column;gap:.35rem}
    .section-heading__title{font-weight:700;color:#1e1b4b}
    .section-heading__title-icon{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:12px;background:rgba(79,70,229,.1);color:#4f46e5;margin-right:.75rem}
    .section-heading__title-icon .icon{width:20px;height:20px}
    .section-heading__meta{display:flex;flex-wrap:wrap;gap:.65rem .95rem;font-size:.85rem;color:#475569}
    .section-heading__meta-item{display:inline-flex;align-items:center;gap:.35rem}
    .section-heading__meta-item .icon{color:#4f46e5}
    .section-heading__desc{color:#6b7280;font-size:.875rem}
    .asset-card__actions{display:flex;align-items:center;justify-content:flex-end;gap:.5rem;flex-wrap:wrap}
    .asset-card__actions .btn{border-radius:10px;font-weight:600;padding-inline:1rem;white-space:nowrap}
    @media (max-width: 767.98px){
      .toggle-content{flex-direction:column;align-items:stretch}
      .toggle-meta{margin-left:0;justify-content:space-between;width:100%}
    }
    @media (max-width: 576px){
      .asset-card__header{flex-direction:column;align-items:stretch}
      .asset-card__actions{justify-content:flex-start}
    }
    .room-assets-summary{display:flex;flex-wrap:wrap;gap:.65rem;font-size:.82rem;color:#64748b;margin-bottom:1rem}
    .room-assets-summary span{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .6rem;border-radius:999px;background:#eef2ff;color:#4338ca;font-weight:600}
    .room-assets-summary svg{color:#4338ca}
    .room-assets-list{display:flex;flex-direction:column;gap:.9rem;margin:0;padding:0;list-style:none}
    .room-asset-item{display:flex;gap:.85rem;align-items:flex-start;padding:.65rem .35rem;border-bottom:1px dashed rgba(148,163,184,.2)}
    .room-asset-item:last-child{border-bottom:none;padding-bottom:0}
    .room-asset-thumb{width:54px;height:54px;border-radius:14px;overflow:hidden;flex-shrink:0;background:#eef2ff;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 0 0 1px rgba(148,163,184,.15)}
    .room-asset-thumb img{width:100%;height:100%;object-fit:cover}
    .room-asset-meta{flex:1;display:flex;flex-direction:column;gap:.35rem}
    .room-asset-meta__name{font-weight:600;color:#1f2937}
    .room-asset-meta__code{font-size:.8rem;color:#6b7280}
    .room-asset-meta__status{font-size:.8rem;color:#475569}
    .room-asset-qty{text-align:right;font-size:.8rem;color:#334155;min-width:110px}
    .room-asset-qty .badge{font-size:.78rem}
    .room-asset-assignees{display:flex;flex-wrap:wrap;gap:.3rem}
    .room-asset-assignee{background:#f1f5f9;color:#0f172a;font-weight:500;border-radius:999px;padding:.2rem .55rem;font-size:.75rem}
    .room-assets-empty{padding:1rem;border-radius:14px;background:#f8fafc;border:1px dashed rgba(148,163,184,.4);font-size:.9rem;color:#64748b;text-align:center}
    @media (max-width: 992px){
      .slots-table th:nth-child(1){width:20%}
      .slots-table th:nth-child(2){width:25%}
      .slots-table th:nth-child(3){width:25%}
      .slots-table th:nth-child(4){width:20%}
      .slots-table th:nth-child(5){width:10%}
      .slots-table th:nth-child(6){width:20%}
    }
  </style>
  @endpush
  <div class="room-detail-layout">
    <div class="room-detail-layout__aside">
      <div class="card shadow-sm">
    @if($phong->hinh_anh)
      <img src="{{ asset('storage/'.$phong->hinh_anh) }}" class="room-cover" alt="{{ $phong->ten_phong }}">
    @endif
    <div class="card-body">
      <div class="room-info-grid">
        <div class="room-info-card room-info-card--zone">
          <div class="room-info-card__icon">
            <i class="fa fa-building"></i>
          </div>
          <div>
            <div class="room-info-card__label">Khu</div>
            <div class="room-info-card__value">{{ optional($phong->khu)->ten_khu ?? '-' }}</div>
          </div>
        </div>
        <div class="room-info-card room-info-card--type">
          <div class="room-info-card__icon">
            <i class="fa fa-bed"></i>
          </div>
          <div>
            <div class="room-info-card__label">Loại phòng</div>
            <div class="room-info-card__value">{{ \App\Models\Phong::labelLoaiPhongBySlots($phong->totalSlots()) }}</div>
          </div>
        </div>
        <div class="room-info-card room-info-card--capacity">
          <div class="room-info-card__icon">
            <i class="fa fa-users"></i>
          </div>
          <div>
            <div class="room-info-card__label">Sức chứa</div>
            <div class="room-info-card__value">{{ $phong->totalSlots() }} người</div>
          </div>
        </div>
        @if(!is_null($phong->gia_phong))
        @php
          $computedPerPersonPrice = null;
          $totalSlots = $phong->totalSlots();
          if (!is_null($phong->gia_moi_nguoi)) {
            $computedPerPersonPrice = $phong->gia_moi_nguoi;
          } elseif (!is_null($phong->gia_phong) && $totalSlots > 0) {
            $computedPerPersonPrice = (int) round($phong->gia_phong / $totalSlots);
          }
        @endphp
        <div class="room-info-card room-info-card--price">
          <div class="room-info-card__icon">
            <i class="fa fa-money"></i>
          </div>
          <div>
            <div class="room-info-card__label">Giá phòng</div>
            <div class="room-info-card__value">{{ number_format($phong->gia_phong, 0, ',', '.') }} VND/tháng</div>
            @if(!is_null($computedPerPersonPrice))
              <div class="room-info-card__meta">
                <span class="room-info-card__meta-badge">
                  <i class="fa fa-user"></i>
                  {{ number_format($computedPerPersonPrice, 0, ',', '.') }} VND/SV · {{ $totalSlots }} chỗ
                </span>
              </div>
            @endif
          </div>
        </div>
        @endif
      </div>
      @php $total=$phong->totalSlots(); $used=$phong->usedSlots(); $pct=$total?round($used*100/$total):0; @endphp
      <div class="room-occupancy">
        <div class="room-occupancy__label">
          <i class="fa fa-chart-pie"></i>
          Tỉ lệ lấp đầy
        </div>
        <div class="room-occupancy__value">{{ $used }} / {{ $total }} ({{ $pct }}%)</div>
        <div class="progress">
          <div class="progress-bar {{ $pct==100 ? 'bg-warning text-dark' : 'bg-success' }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>
      @if($phong->ghi_chu)
        <ul class="list-unstyled mb-0 mt-2 small text-muted">
          <li><i class="fa fa-sticky-note-o me-1"></i>{{ $phong->ghi_chu }}</li>
        </ul>
      @endif
    </div>
      </div>
      <div class="card shadow-sm room-assets-card">
        <div class="card-header bg-white border-0">
          <div class="asset-card__header">
            <button
              class="card-toggle asset-card__collapse-trigger"
              type="button"
              data-bs-toggle="collapse"
              data-toggle="collapse"
              data-bs-target="#roomCommonAssetsCollapse"
              data-target="#roomCommonAssetsCollapse"
              aria-expanded="true"
              aria-controls="roomCommonAssetsCollapse"
              data-role="room-assets-toggle"
            >
              <div class="toggle-content">
                <div class="section-heading">
                  <h5 class="section-heading__title mb-1">
                    <span class="section-heading__title-icon">
                      <svg class="icon icon--lg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M11.47 3.84a.75.75 0 0 1 1.06 0l8.25 7.88a.75.75 0 0 1-.52 1.28H19.5v7a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-2a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1-.75-.75v-7h-1.76a.75.75 0 0 1-.52-1.28l8.25-7.88Z" />
                      </svg>
                    </span>
                    Tài sản chung  phòng {{ $phong->ten_phong }}
                  </h5>
                  <div class="section-heading__meta">
                  </div>
                  <p class="section-heading__desc mb-0">Danh sách vật dụng sử dụng chung trong phòng</p>
                </div>
              </div>
            </button>
          </div>
        </div>
        <div id="roomCommonAssetsCollapse" class="collapse show">
          <div class="card-body">
          @php
            $totalCommonQuantity = $commonAssetStats['total_quantity'] ?? 0;
            $totalCommonAssigned = $commonAssetStats['total_assigned'] ?? 0;
            $roomAssetPlaceholder = asset('uploads/default.png');
          @endphp
          <div class="room-assets-summary">
            <span>
              <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 4.5c-1.243 0-2.25 1.007-2.25 2.25S10.757 9 12 9s2.25-1.007 2.25-2.25S13.243 4.5 12 4.5Zm0-1.5a3.75 3.75 0 1 1 0 7.5 3.75 3.75 0 0 1 0-7.5ZM4.5 18.75a7.5 7.5 0 0 1 15 0a.75.75 0 0 1-1.5 0a6 6 0 1 0-12 0a.75.75 0 0 1-1.5 0Z"/></svg>
              Tổng {{ $totalCommonQuantity }} món
            </span>
          </div>
          @if($roomAssets->isEmpty())
            <div class="room-assets-empty">
              Phòng chưa được cấp tài sản chung nào từ kho.
            </div>
          @else
            <ul class="room-assets-list">
              @foreach($roomAssets as $asset)
                @php
                  $assetName = $asset->ten_tai_san ?? optional($asset->khoTaiSan)->ten_tai_san ?? 'Chưa cập nhật tên';
                  $assetCode = optional($asset->khoTaiSan)->ma_tai_san ?? ($asset->ma_tai_san ?? '—');
                  $rawImage = $asset->hinh_anh ?? optional($asset->khoTaiSan)->hinh_anh;
                  $assetImage = $roomAssetPlaceholder;
                  if (!empty($rawImage)) {
                      $assetImage = filter_var($rawImage, FILTER_VALIDATE_URL)
                        ? $rawImage
                        : asset('storage/' . ltrim($rawImage, '/'));
                  }
                  $assignedQty = (int) ($asset->assigned_slot_quantity ?? $asset->slots->sum(function ($slot) {
                      return (int) ($slot->pivot->so_luong ?? 0);
                  }));
                  $availableQty = (int) ($asset->available_quantity ?? max(0, (int) $asset->so_luong - $assignedQty));
                @endphp
                <li class="room-asset-item">
                  <div class="room-asset-thumb">
                    <img src="{{ $assetImage }}" alt="Ảnh {{ $assetName }}">
                  </div>
                  <div class="room-asset-meta">
                    <div class="room-asset-meta__name">{{ $assetName }}</div>
                    <div class="room-asset-meta__code">Mã: {{ $assetCode }}</div>
                    <div class="room-asset-meta__status">
                      Tình trạng chuẩn: {{ $asset->tinh_trang ?? 'Chưa cập nhật' }}
                      @if(!empty($asset->tinh_trang_hien_tai))
                        · Hiện tại: {{ $asset->tinh_trang_hien_tai }}
                      @endif
                    </div>
                    @if($assignedQty > 0)
                      <div class="room-asset-assignees">
                        @foreach($asset->slots as $slot)
                          @php
                            $pivotQty = (int) ($slot->pivot->so_luong ?? 0);
                            if ($pivotQty <= 0) {
                                continue;
                            }
                            $studentName = optional($slot->sinhVien)->ho_ten;
                            $badgeTitle = $studentName
                              ? $slot->ma_slot . ' · ' . $studentName . ' · x' . $pivotQty
                              : $slot->ma_slot . ' · x' . $pivotQty;
                          @endphp
                          <span class="room-asset-assignee" title="{{ $badgeTitle }}">
                            {{ $slot->ma_slot }} x{{ $pivotQty }}
                          </span>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
        </div>
      </div>
    </div>
    <div class="room-detail-layout__main">
      {{-- Danh sách slot --}}
      <div class="khu-toolbar mb-3">
        <div class="d-flex justify-content-end">
          <a href="#" class="btn btn-dergin btn-dergin--info" onclick="openCreateSlots()"><i class="fa fa-plus"></i><span>Tạo slots</span></a>
        </div>
      </div>
      <div class="khu-rooms-wrapper">
          <table class="table align-middle khu-rooms-table">
            <thead>
              <tr>
                <th>Mã slot</th>
                <th>Sinh viên</th>
                <th>CSVC (bàn giao)</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($phong->slots as $slot)
              <tr class="slot-row {{ $slot->sinh_vien_id ? 'occupied' : 'empty' }}">
                <td data-label="Mã slot">{{ $slot->ma_slot }}</td>
                <td data-label="Sinh viên">{{ $slot->sinhVien->ho_ten ?? '-' }}</td>
                <td data-label="CSVC (bàn giao)">
                  @if(($slot->taiSans ?? collect())->count() > 0)
                    <style>
                      .chip{display:inline-flex;align-items:center;gap:.35rem;border:1px solid #e9ecef;border-radius:999px;padding:.15rem .6rem;margin:.12rem;background:#fff;max-width:100%}
                      .chip img{width:20px;height:20px;border-radius:50%;object-fit:cover;border:1px solid #e9ecef}
                      .chip .name{font-size:12px;color:#212529;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px}
                      .chip .code{font-size:11px;color:#6c757d}
                      .chip .qty{font-size:11px;color:#6c757d}
                    </style>
                    <div class="d-flex flex-wrap">
                      @foreach($slot->taiSans as $ts)
                        @php
                          $qty = (int)($ts->pivot->so_luong ?? 0);
                          $code = optional($ts->khoTaiSan)->ma_tai_san ?? ('TS-'.$ts->id);
                          $img = $ts->hinh_anh ? asset('storage/'.$ts->hinh_anh) : (optional($ts->khoTaiSan)->hinh_anh ? asset('storage/'.optional($ts->khoTaiSan)->hinh_anh) : null);
                        @endphp
                        <span class="chip" title="{{ $ts->ten_tai_san }} ({{ $code }}) x{{ $qty }}">
                          @if($img)
                            <img src="{{ $img }}" alt="{{ $ts->ten_tai_san }}">
                          @endif
                          <span class="name">{{ $ts->ten_tai_san }}</span>
                          <span class="code">{{ $code }}</span>
                          <span class="qty">x{{ $qty }}</span>
                        </span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td data-label="Ghi chú"><span class="text-trunc" title="{{ $slot->ghi_chu }}">{{ $slot->ghi_chu }}</span></td>
                <td data-label="Thao tác">
                  <div class="slot-actions" role="group">
                    <button type="button" class="btn-dergin" title="Bàn giao CSVC" onclick="openAssignAssets({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-eye"></i><span>CSVC</span></button>
                    @if (!$slot->sinh_vien_id)
                      <button type="button" class="btn-dergin btn-dergin--info" title="Gán sinh viên" onclick="openAssignStudent({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-user-plus"></i><span>Gán SV</span></button>
                    @else
                      <button type="button" class="btn-dergin btn-dergin--danger" title="Bỏ gán sinh viên" onclick="unassignStudent({{ $slot->id }})"><i class="fa fa-trash"></i><span>Bỏ gán</span></button>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>
{{-- Modal tạo slots đơn giản --}}
<div class="modal fade" id="createSlotsQuickModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createSlotsQuickForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header"><h5>Tạo slots cho phòng</h5></div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Số lượng slot cần tạo</label>
            <input type="number" min="1" max="50" class="form-control" name="count" value="1" required>
            <small class="text-muted">Mã sẽ tự sinh: {{ $phong->ten_phong }}-S{n} tiếp nối số đang có.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Tạo</button>
        </div>
      </div>
    </form>
  </div>
  </div>
{{-- Modal chọn sinh viên --}}
<div class="modal fade" id="assignStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="assignStudentForm">
      @csrf
      <input type="hidden" name="slot_id" id="modal_slot_id">
      <div class="modal-content">
        <div class="modal-header"><h5>Chọn sinh viên để gán vào slot</h5></div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Sinh viên</label>
            <select class="form-select assign-student-select" id="modal_sinh_vien_id" name="sinh_vien_id" required>
              <option value="">--Chọn sinh viên--</option>
              @foreach($sinhViens as $sv)
                <option value="{{ $sv->id }}">{{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }}){{ $sv->gioi_tinh ? ' - '.$sv->gioi_tinh : '' }}</option>
              @endforeach
            </select>
          </div>
          {{-- Bỏ chọn CSVC/ảnh tại đây theo yêu cầu --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Gán</button>
        </div>
      </div>
    </form>
  </div>
</div>
{{-- Modal bàn giao CSVC cho slot (đặt ngoài các modal khác) --}}
<div class="modal fade" id="assignAssetsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="assignAssetsForm" class="modal-content">
      @csrf
      <input type="hidden" id="assign_assets_slot_id" name="slot_id">
      <div class="modal-header">
        <h5 class="modal-title">Bổ sung tài sản cho slot <span id="assign_assets_slot_label" class="text-primary"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
        <div class="modal-body">
        <div class="slot-current-assets mb-4 d-none" data-role="current-assets-wrapper">
          <div class="slot-current-assets__title">CSVC đã bàn giao cho slot</div>
          <div class="slot-current-assets__list" data-role="current-assets-list"></div>
        </div>
        <div class="asset-modal asset-modal--compact">
          <div class="row g-4">
            <div class="col-lg-7">
              <div class="asset-modal__column">
                <h6 class="asset-modal__heading">Danh sách tài sản trong kho</h6>
                <div class="asset-modal__search mb-3">
                  <input type="search" class="form-control form-control-sm" placeholder="Tìm kiếm tài sản..." data-role="asset-search" autocomplete="off" disabled>
                </div>
                <div class="asset-option-list" data-role="asset-picker">
                  <div class="asset-empty-message">Đang tải dữ liệu kho...</div>
                </div>
                <p class="text-muted small mt-3 d-none" data-role="asset-search-empty">Không tìm thấy tài sản phù hợp.</p>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="asset-modal__column">
                <h6 class="asset-modal__heading">Tài sản sẽ bàn giao</h6>
                <div class="selected-assets" data-role="selected-assets">
                  <p class="text-muted small mb-0" data-role="empty-state">Chưa chọn tài sản nào.</p>
          </div>
                <p class="text-muted small mt-3 mb-0">Mỗi tài sản được bàn giao 1 món từ kho tổng.</p>
          </div>
          </div>
          </div>
          </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary" data-role="asset-submit" disabled>Bổ sung cho slot</button>
      </div>
    </form>
  </div>
  </div>
{{-- (Đã bỏ modal sửa slot) --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const collapseEl = document.getElementById('roomCommonAssetsCollapse');
    const toggleBtn = document.querySelector('[data-role="room-assets-toggle"]');
    if (!collapseEl || !toggleBtn) {
      return;
    }
    const hasBootstrap = !!(window.bootstrap && bootstrap.Collapse);
    const hasjQueryCollapse = !hasBootstrap && window.jQuery && typeof window.jQuery.fn.collapse === 'function';
    if (hasBootstrap) {
      bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
    } else if (hasjQueryCollapse) {
      window.jQuery(collapseEl).collapse({ toggle: false });
    }
    toggleBtn.addEventListener('click', function (event) {
      if (hasBootstrap) {
        return; // Bootstrap data API will handle the toggle
      }
      if (hasjQueryCollapse) {
        event.preventDefault();
        window.jQuery(collapseEl).collapse('toggle');
      } else {
        event.preventDefault();
        collapseEl.classList.toggle('show');
        updateState();
      }
    });
    const updateState = () => {
      const expanded = collapseEl.classList.contains('show');
      toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    };
    collapseEl.addEventListener('shown.bs.collapse', updateState);
    collapseEl.addEventListener('hidden.bs.collapse', updateState);
    updateState();
  });
  let currentSlotId = null;
  function openCreateSlots(){
    $('#createSlotsQuickModal').modal('show');
  }
  $('#createSlotsQuickForm').submit(function(e){
    e.preventDefault();
    const phongId = {{ $phong->id }};
    const count = parseInt($(this).find('input[name=count]').val()||'0',10);
    if(count<=0){ alert('Số lượng không hợp lệ'); return; }
    const existing = {{ $phong->slots->count() }};
    const codes = [];
    for(let i=1;i<=count;i++){
      const idx = existing + i; // tiếp nối số hiện có
      codes.push('{{ $phong->ten_phong }}-S' + idx);
    }
    const requests = codes.map(code => $.ajax({ url:'/admin/phong/'+phongId+'/slots', method:'POST', data:{ _token:'{{ csrf_token() }}', ma_slot: code }}));
    Promise.allSettled(requests).then(()=> location.reload());
  });
  let __currentAssign = { id: null, ma: '' };
  function openAssignStudent(slotId, maSlot){
    __currentAssign = { id: slotId, ma: maSlot||'' };
    $('#modal_slot_id').val(slotId);
    $('#modal_sinh_vien_id').val('');
    $('#assignStudentModal').modal('show');
  }
  $('#assignStudentForm').submit(function(e){
    e.preventDefault();
    let slotId = $('#modal_slot_id').val();
    let sinhVienId = $('#modal_sinh_vien_id').val();
    if(!sinhVienId) return alert('Chọn sinh viên!');
    const formData = new FormData();
    formData.append('_token','{{ csrf_token() }}');
    formData.append('sinh_vien_id', sinhVienId);
    fetch('/admin/slots/'+slotId+'/assign', { method:'POST', body: formData })
      .then(r=>{ if(!r.ok) throw r; return r.json(); })
      .then(()=> location.reload())
      .catch(async (err)=>{ try{ const j=await err.json(); alert(j.message||'Lỗi'); } catch{ alert('Lỗi'); } });
  });
  // (Đã bỏ nút mở CSVC từ modal gán sinh viên)
  // Bàn giao CSVC từ kho tổng
  const assignAssetsModalEl = document.getElementById('assignAssetsModal');
  const assignAssetsForm = document.getElementById('assignAssetsForm');
  const assetPickerEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-picker"]') : null;
  const selectedAssetsEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="selected-assets"]') : null;
  const searchInputEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-search"]') : null;
  const searchEmptyEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-search-empty"]') : null;
  const submitBtnEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-submit"]') : null;
  const currentAssetsWrapper = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="current-assets-wrapper"]') : null;
  const currentAssetsList = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="current-assets-list"]') : null;
  const assetPlaceholder = '{{ asset('uploads/default.png') }}';

  let assignAssetSelection = new Map();
  let emptyStateEl = null;

  const escapeHtml = (value) => {
    return (value ?? '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  };

  const createEmptyStateElement = () => {
    const p = document.createElement('p');
    p.className = 'text-muted small mb-0';
    p.dataset.role = 'empty-state';
    p.textContent = 'Chưa chọn tài sản nào.';
    return p;
  };

  const updateSubmitState = () => {
    if (submitBtnEl) {
      submitBtnEl.disabled = assignAssetSelection.size === 0;
    }
  };

  const updateEmptyState = () => {
    if (!emptyStateEl) {
      return;
    }
    emptyStateEl.classList.toggle('d-none', assignAssetSelection.size > 0);
  };

  const resetSelection = () => {
    assignAssetSelection = new Map();
    if (!selectedAssetsEl) {
      return;
    }
    selectedAssetsEl.innerHTML = '';
    emptyStateEl = createEmptyStateElement();
    selectedAssetsEl.appendChild(emptyStateEl);
    updateEmptyState();
    updateSubmitState();
  };

  const setOptionState = (optionEl, isSelected) => {
    if (!optionEl) {
      return;
    }
    const btn = optionEl.querySelector('[data-role="asset-toggle"]');
    if (isSelected) {
      optionEl.classList.add('is-selected');
      if (btn) {
        btn.textContent = 'Đã chọn';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
      }
    } else {
      optionEl.classList.remove('is-selected');
      if (btn) {
        btn.textContent = 'Chọn';
        btn.classList.add('btn-outline-primary');
        btn.classList.remove('btn-primary');
      }
    }
  };

  const removeSelectedItem = (id) => {
    const entry = assignAssetSelection.get(id);
    if (!entry) {
      return;
    }
    if (entry.wrapper && entry.wrapper.parentNode) {
      entry.wrapper.parentNode.removeChild(entry.wrapper);
    }
    assignAssetSelection.delete(id);
    updateEmptyState();
    updateSubmitState();
  };

  const createSelectedItem = (optionEl) => {
    if (!selectedAssetsEl) {
      return;
    }
    const id = optionEl.dataset.id;
    const name = optionEl.dataset.name || 'Không xác định';
    const code = optionEl.dataset.code || 'N/A';
    const stock = optionEl.dataset.stock || '0';
    const condition = optionEl.dataset.condition || 'Không rõ';
    const image = optionEl.dataset.image || assetPlaceholder;

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

    const actions = document.createElement('div');
    actions.className = 'selected-asset__actions';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-outline-secondary btn-sm';
    removeBtn.textContent = 'Bỏ chọn';
    removeBtn.addEventListener('click', () => {
      removeSelectedItem(id);
      setOptionState(optionEl, false);
    });

    actions.appendChild(removeBtn);

    top.appendChild(thumb);
    top.appendChild(info);
    top.appendChild(actions);

    wrapper.appendChild(top);

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = `assets[${id}]`;
    hiddenInput.value = '1';
    wrapper.appendChild(hiddenInput);

    selectedAssetsEl.appendChild(wrapper);
    assignAssetSelection.set(id, { wrapper, option: optionEl });
    updateEmptyState();
    updateSubmitState();
  };

  const toggleSelection = (optionEl) => {
    const id = optionEl.dataset.id;
    if (!id) {
      return;
    }
    const alreadySelected = assignAssetSelection.has(id);
    if (alreadySelected) {
      removeSelectedItem(id);
      setOptionState(optionEl, false);
      return;
    }
    const stock = parseInt(optionEl.dataset.stock || '0', 10);
    if (stock <= 0) {
      return;
    }
    createSelectedItem(optionEl);
    setOptionState(optionEl, true);
  };

  const bindOptionEvents = (optionEl) => {
    const btn = optionEl.querySelector('[data-role="asset-toggle"]');
    if (btn && !btn.disabled) {
      btn.addEventListener('click', (event) => {
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
  };

  const applySearchFilter = () => {
    if (!assetPickerEl || !searchInputEl) {
      return;
    }
    const keyword = searchInputEl.value.trim().toLowerCase();
    let visible = 0;
    assetPickerEl.querySelectorAll('[data-role="asset-item"]').forEach((optionEl) => {
      const name = (optionEl.dataset.name || '').toLowerCase();
      const code = (optionEl.dataset.code || '').toLowerCase();
      const condition = (optionEl.dataset.condition || '').toLowerCase();
      const matches = !keyword || name.includes(keyword) || code.includes(keyword) || condition.includes(keyword);
      optionEl.classList.toggle('d-none', !matches);
      if (matches) {
        visible += 1;
      }
    });
    if (searchEmptyEl) {
      searchEmptyEl.classList.toggle('d-none', visible !== 0);
    }
  };

  if (searchInputEl) {
    searchInputEl.addEventListener('input', applySearchFilter);
  }

  const buildOptionElement = (asset) => {
    const optionEl = document.createElement('div');
    optionEl.className = 'asset-option';
    optionEl.dataset.role = 'asset-item';
    optionEl.dataset.id = asset.id;
    optionEl.dataset.name = asset.ten_tai_san || '';
    optionEl.dataset.code = asset.ma_tai_san || '';
    optionEl.dataset.stock = asset.so_luong ?? 0;
    optionEl.dataset.condition = asset.tinh_trang || '';
    optionEl.dataset.image = asset.hinh_anh || assetPlaceholder;

    const safeName = escapeHtml(asset.ten_tai_san || 'Không xác định');
    const safeCode = escapeHtml(asset.ma_tai_san || 'N/A');
    const safeCondition = escapeHtml(asset.tinh_trang || 'Không rõ');
    const stock = parseInt(optionEl.dataset.stock || '0', 10);

    optionEl.innerHTML = `
      <div class="asset-option__body">
        <div class="asset-option__thumb">
          <img src="${asset.hinh_anh || assetPlaceholder}" alt="${safeName}">
        </div>
        <div class="asset-option__details">
          <div class="asset-option__title">${safeName}</div>
          <div class="asset-option__meta">Mã: ${safeCode} · Còn: ${Number.isFinite(stock) ? stock : 0}</div>
          <div class="asset-option__condition">Tình trạng: ${safeCondition}</div>
        </div>
        <div class="asset-option__actions">
          <button type="button" class="btn btn-outline-primary btn-sm" data-role="asset-toggle">Chọn</button>
        </div>
      </div>
    `;

    if (stock <= 0) {
      optionEl.classList.add('is-disabled');
      const btn = optionEl.querySelector('[data-role="asset-toggle"]');
      if (btn) {
        btn.textContent = 'Hết hàng';
        btn.disabled = true;
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-secondary');
      }
    }

    return optionEl;
  };

  const renderAssetList = (assets) => {
    if (!assetPickerEl) {
      return;
    }
    assetPickerEl.innerHTML = '';
    resetSelection();

    if (!Array.isArray(assets) || assets.length === 0) {
      assetPickerEl.innerHTML = '<div class="asset-empty-message">Kho hiện chưa có tài sản khả dụng.</div>';
      if (searchInputEl) {
        searchInputEl.value = '';
        searchInputEl.disabled = true;
      }
      if (searchEmptyEl) {
        searchEmptyEl.classList.add('d-none');
      }
      return;
    }

    const fragment = document.createDocumentFragment();
    assets.forEach((asset) => {
      const optionEl = buildOptionElement(asset);
      fragment.appendChild(optionEl);
    });

    assetPickerEl.appendChild(fragment);
    assetPickerEl.querySelectorAll('[data-role="asset-item"]').forEach(bindOptionEvents);

    if (searchInputEl) {
      searchInputEl.disabled = false;
      searchInputEl.value = '';
    }
    if (searchEmptyEl) {
      searchEmptyEl.classList.add('d-none');
    }
    applySearchFilter();
  };

  const renderCurrentAssets = (items) => {
    if (!currentAssetsWrapper || !currentAssetsList) {
      return;
    }
    currentAssetsList.innerHTML = '';
    if (!Array.isArray(items) || items.length === 0) {
      currentAssetsWrapper.classList.add('d-none');
      return;
    }

    currentAssetsWrapper.classList.remove('d-none');
    const fragment = document.createDocumentFragment();

    items.forEach((item) => {
      const card = document.createElement('div');
      card.className = 'slot-current-asset';

      const thumb = document.createElement('div');
      thumb.className = 'slot-current-asset__thumb';
      const img = document.createElement('img');
      img.src = item.hinh_anh || assetPlaceholder;
      img.alt = item.ten_tai_san || 'Tài sản';
      thumb.appendChild(img);

      const info = document.createElement('div');
      info.className = 'slot-current-asset__info';

      const nameEl = document.createElement('div');
      nameEl.className = 'slot-current-asset__name';
      nameEl.textContent = item.ten_tai_san || 'Không rõ';
      if (!item.is_from_warehouse) {
        const badge = document.createElement('span');
        badge.className = 'slot-current-asset__badge';
        badge.textContent = 'Thêm thủ công';
        nameEl.appendChild(badge);
      }

      const metaEl = document.createElement('div');
      metaEl.className = 'slot-current-asset__meta';
      const metaParts = [];
      if (item.ma_tai_san) {
        metaParts.push('Mã: ' + item.ma_tai_san);
      }
      const quantity = parseInt(item.so_luong || 0, 10);
      metaParts.push('Số lượng: ' + (Number.isFinite(quantity) ? quantity : 0));
      if (item.tinh_trang) {
        metaParts.push('Tình trạng: ' + item.tinh_trang);
      }
      metaEl.textContent = metaParts.join(' · ');

      info.appendChild(nameEl);
      info.appendChild(metaEl);

      const actions = document.createElement('div');
      actions.className = 'slot-current-asset__actions';
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-outline-danger';
      removeBtn.title = 'Xóa tài sản khỏi slot';
      removeBtn.innerHTML = `
        <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7.5h12" />
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 7.5V5.25A1.5 1.5 0 0 1 11.25 3.75h1.5a1.5 1.5 0 0 1 1.5 1.5V7.5" />
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7.5V19.5A1.5 1.5 0 0 0 9 21h6a1.5 1.5 0 0 0 1.5-1.5V7.5" />
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 11.25v6" />
          <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 11.25v6" />
        </svg>
        <span>Xóa</span>
      `;
      removeBtn.addEventListener('click', () => {
        if (!currentSlotId || !item.tai_san_id) return;
        if (!confirm('Bạn có chắc muốn xóa tài sản này khỏi slot?')) return;
        const formData = new FormData();
        formData.append('tai_san_id', item.tai_san_id);
        fetch('/admin/slots/' + currentSlotId + '/return-asset', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
          body: formData,
        })
          .then((res) => { if (!res.ok) throw new Error('Request failed'); return res.json().catch(()=>({})); })
          .then(() => location.reload())
          .catch(() => alert('Không thể xóa tài sản khỏi slot.'));
      });
      actions.appendChild(removeBtn);

      card.appendChild(thumb);
      card.appendChild(info);
      card.appendChild(actions);

      fragment.appendChild(card);
    });

    currentAssetsList.appendChild(fragment);
  };

  function openAssignAssets(slotId, maSlot){
    if (!assignAssetsModalEl) {
      return;
    }
    currentSlotId = slotId;
    document.getElementById('assign_assets_slot_id').value = slotId;
    document.getElementById('assign_assets_slot_label').textContent = maSlot || '';

    if (assetPickerEl) {
      assetPickerEl.innerHTML = '<div class="asset-empty-message">Đang tải dữ liệu kho...</div>';
    }
    if (searchInputEl) {
      searchInputEl.value = '';
      searchInputEl.disabled = true;
    }
    if (searchEmptyEl) {
      searchEmptyEl.classList.add('d-none');
    }
    if (submitBtnEl) {
      submitBtnEl.disabled = true;
    }
    resetSelection();
    if (currentAssetsWrapper) {
      currentAssetsWrapper.classList.add('d-none');
    }
    if (currentAssetsList) {
      currentAssetsList.innerHTML = '';
    }

    $('#assignAssetsModal').modal('show');

    fetch('/admin/slots/'+slotId+'/warehouse-assets', { headers: { 'Accept': 'application/json' } })
      .then(async (response) => {
        if (!response.ok) {
          const payload = await response.json().catch(() => ({}));
          const message = payload && payload.message ? payload.message : 'Không thể tải dữ liệu kho.';
          throw new Error(message);
        }
        return response.json();
      })
      .then((data) => {
        renderAssetList(data.warehouse_assets || []);
        renderCurrentAssets(data.assigned_assets || []);
      })
      .catch((error) => {
        if (assetPickerEl) {
          assetPickerEl.innerHTML = '<div class="asset-empty-message text-danger">'+escapeHtml(error.message || 'Không thể tải dữ liệu kho.')+'</div>';
        }
      });
  }

  if (assignAssetsForm) {
    assignAssetsForm.addEventListener('submit', function(e){
      e.preventDefault();
      const slotId = document.getElementById('assign_assets_slot_id').value;
      if (!slotId) {
        alert('Không xác định được slot để bổ sung tài sản.');
        return;
      }
      if (assignAssetSelection.size === 0) {
        alert('Vui lòng chọn ít nhất 1 tài sản từ kho.');
        return;
      }

      if (submitBtnEl) {
        submitBtnEl.disabled = true;
      }

      const payload = {
        slot_id: slotId,
        assets: {},
      };

      assignAssetSelection.forEach((entry, id) => {
        payload.assets[id] = 1;
      });

      fetch('{{ route('slots.importFromWarehouse') }}', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(payload),
      })
        .then(async (response) => {
          if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || 'Không thể bổ sung tài sản cho slot.');
          }
          return response.json().catch(() => ({}));
        })
        .then(() => {
          location.reload();
        })
        .catch((error) => {
          alert(error.message || 'Không thể bổ sung tài sản cho slot.');
          if (submitBtnEl) {
            submitBtnEl.disabled = false;
          }
        });
    });
  }
  // (Đã bỏ tính năng sửa slot)
  // Bỏ gán sinh viên
  function unassignStudent(slotId){
    if(!confirm('Bạn chắc chắn xoá sinh viên khỏi slot này?')) return;
    $.ajax({
      url: '/admin/slots/'+slotId+'/assign',
      method:'POST',
      data:{ _token:'{{ csrf_token() }}', sinh_vien_id:'' },
      success:()=>location.reload(),
      error:x=>alert(x.responseJSON?.message||'Lỗi')
    });
  }

  // Bỏ toàn bộ CSVC slot
  function clearAssets(slotId){
    if(!confirm('Bỏ gán toàn bộ CSVC cho slot này?')) return;
    fetch('/admin/slots/'+slotId+'/clear-assets', { method:'POST', headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' } })
      .then(r=>{ if(!r.ok) throw new Error('Lỗi'); return r.json(); })
      .then(()=> location.reload())
      .catch(()=> alert('Không thể bỏ CSVC'));
  }
</script>
@endpush
@endsection
