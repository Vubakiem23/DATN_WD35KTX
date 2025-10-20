
@extends('admin.layouts.admin')

@section('title','Quản lý phòng')

@section('content')
<div class="container-fluid">

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    <script>setTimeout(()=>window.showToast(@json(session('status')),'success'),0)</script>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{!! session('error') !!}</div>
    <script>setTimeout(()=>window.showToast(@json(strip_tags(session('error'))),'error'),0)</script>
  @endif

  {{--  --}}

  {{-- Extra prominent create button to ensure visibility --}}
  

  {{-- Tabs by Khu (building) --}}
  <h4>Khu</h4>
  @php
    $khuList = $phongs->groupBy('khu');
    $firstKhu = $khuList->keys()->first() ?? '';
  @endphp

  <ul class="nav nav-tabs mb-3" id="khuTabs" role="tablist">
    @foreach($khuList->keys() as $k => $khu)
      @php $slug = \Illuminate\Support\Str::slug($khu) ?: 'khu-'.$k; @endphp
      <li class="nav-item" role="presentation">
  <button class="nav-link {{ $khu == $firstKhu ? 'active' : '' }}" id="tab-{{ $slug }}"
    data-bs-toggle="tab" data-bs-target="#khu-{{ $slug }}"
    data-toggle="tab" data-target="#khu-{{ $slug }}"
    type="button" role="tab">{{ $khu ?: 'Không xác định' }}</button>
      </li>
    @endforeach
  </ul>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Danh sách các phòng</h4>
    <a href="{{ route('phong.create') }}" class="btn btn-success">Tạo phòng</a>
  </div>
  <div class="tab-content">
    @foreach($khuList as $k => $items)
      @php $khu = $items->first()->khu ?? ''; $slug = \Illuminate\Support\Str::slug($khu) ?: 'khu-'.$k; @endphp
      <div class="tab-pane fade {{ $khu == $firstKhu ? 'show active' : '' }}" id="khu-{{ $slug }}" role="tabpanel">
        <div class="row g-3">
          @foreach($items as $p)
            <div class="col-12 col-md-6 col-lg-4">
              <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <strong>{{ $p->ten_phong }}</strong>
                  <span class="badge bg-{{ $p->availableSlots() == 0 && $p->totalSlots() > 0 ? 'warning text-dark' : 'success' }}">{{ $p->occupancyLabel() }}</span>
                </div>
                @if(!empty($p->hinh_anh))
                  <img src="{{ asset('storage/'.$p->hinh_anh) }}" class="card-img-top" style="height:160px;object-fit:cover" alt="{{ $p->ten_phong }}">
                @else
                  <div class="card-img-top d-flex align-items-center justify-content-center" style="height:160px;background:#f8f9fa">
                    {{-- inline SVG placeholder so image always shows even if no file --}}
                    <svg width="80" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <rect width="24" height="24" rx="2" fill="#e9ecef"/>
                      <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                @endif
                <div class="card-body">
                  <p class="mb-1"><strong>Khu:</strong> {{ $p->khu ?? '-' }}</p>
                  <p class="mb-1"><strong>Loại:</strong> {{ $p->loai_phong ?? '-' }}</p>
                  <p class="mb-1"><strong>Sức chứa:</strong> {{ $p->totalSlots() }} chỗ</p>
                  <p class="mb-1"><strong>Hiện tại:</strong> {{ $p->usedSlots() }} / {{ $p->totalSlots() }}</p>
                  @if($p->ghi_chu)
                    <p class="text-muted small">{{ Str::limit($p->ghi_chu, 120) }}</p>
                  @endif
                </div>
                <div class="card-footer d-flex gap-2">
                  <a href="{{ route('phong.edit', $p) }}" class="btn btn-sm btn-info flex-fill">Chỉnh sửa</a>
                  <a href="{{ route('phong.show', $p->id) }}" class="btn btn-sm btn-secondary flex-fill">Thông Tin</a>

                  <a href="{{ route('taisan.index') }}?phong_id={{ $p->id }}" class="btn btn-sm btn-primary flex-fill">Tài Sản</a>
                  <form action="{{ route('phong.destroy', $p) }}" method="POST" onsubmit="return confirm('Bạn không thể hoàn tác. Xóa phòng?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  {{-- Bỏ phân trang theo yêu cầu --}}

</div>

@push('scripts')
<script>
function openAddGuest(phongId){
  const url = '/sinhvien/create?phong_id=' + phongId;
  window.location.href = url;
}

// Ensure tabs activate across Bootstrap versions (fallback)
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('#khuTabs button').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var target = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
      if(!target) return;
      // hide other panes
      document.querySelectorAll('.tab-pane').forEach(function(p){ p.classList.remove('show','active'); });
      var pane = document.querySelector(target);
      if(pane){ pane.classList.add('show','active'); }
      document.querySelectorAll('#khuTabs button').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
    });
  });
});
</script>
@endpush

@endsection
