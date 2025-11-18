    @extends('admin.layouts.admin')

    @section('title', 'Thêm thông báo')

    @section('content')
<div class="notification-form-wrapper">
<div class="mb-4">
    <div class="mb-5">
        <h3 class="room-page__title mb-2">Thêm Thông Báo</h3>
        <p class="text-muted mb-0">Theo dõi toàn bộ thông báo, mức độ, phòng/khu và người viết.</p>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('thongbao.store') }}" method="POST" enctype="multipart/form-data" id="thongbao-form" class="notification-form-card">
            @csrf
        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Thông tin chính</p>
                    <h5 class="nf-section-title">Nội dung & phạm vi hiển thị</h5>
                </div>
                <span class="nf-chip nf-chip--subtle">Bắt buộc</span>
            </div>

            <div class="row g-4">
                {{-- Tiêu đề --}}
                <div class="col-12">
                    <label class="form-label">Tiêu đề</label>
                    <div class="nf-inline-controls flex-wrap gap-2">
                        <select name="tieu_de_id" id="tieu_de_id" class="form-select flex-grow-1">
                            <option value="">-- Chọn tiêu đề --</option>
                            @foreach($tieuDes as $td)
                            <option value="{{ $td->id }}">{{ $td->ten_tieu_de }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="add_title_btn" class="btn btn-outline-primary">+ Thêm</button>
                        <button type="button" id="delete_title_btn" class="btn btn-outline-danger">Xóa</button>
                    </div>
                    <input type="text" id="input_tieu_de" class="form-control mt-2" style="display:none;" placeholder="Nhập tiêu đề mới và Enter để lưu">
                </div>

                {{-- Mức độ --}}
                <div class="col-12">
                    <label class="form-label">Mức độ (tùy chọn)</label>
                    <div class="nf-inline-controls flex-wrap gap-2">
                        <select name="muc_do_id" id="muc_do_id" class="form-select flex-grow-1">
                            <option value="">-- Không chọn mức độ --</option>
                            @foreach($mucDos as $md)
                            <option value="{{ $md->id }}" {{ old('muc_do_id') == $md->id ? 'selected' : '' }}>
                                {{ $md->ten_muc_do }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" id="add_priority_btn" class="btn btn-outline-success">+ Thêm</button>
                        <button type="button" id="delete_priority_btn" class="btn btn-outline-danger">Xóa</button>
                    </div>
                    <input type="text" id="input_muc_do" class="form-control mt-2" style="display:none;" placeholder="Nhập mức độ mới và Enter để lưu">
                </div>

                {{-- Nội dung --}}
                <div class="col-12">
                    <label class="form-label">Nội dung</label>
                    <textarea id="noi_dung" name="noi_dung" class="form-control" rows="6">{{ old('noi_dung') }}</textarea>
                    <small class="nf-hint-text">Bạn có thể chèn hình ảnh, liên kết hoặc định dạng văn bản trực tiếp trong trình soạn thảo.</small>
                </div>
            </div>
        </div>

        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Lịch & đối tượng nhận</p>
                    <h5 class="nf-section-title">Gửi đến đúng người, đúng thời điểm</h5>
                </div>
            </div>

            <div class="row g-4">
                {{-- Ngày đăng --}}
                <div class="col-md-6">
                    <label class="form-label">Ngày đăng</label>
                    <input type="date" name="ngay_dang" class="form-control" value="{{ old('ngay_dang') }}" required>
                </div>

                {{-- Đối tượng --}}
                <div class="col-md-6">
                    <label class="form-label">Đối tượng</label>
                    <select name="doi_tuong" class="form-select" required>
                        <option value="">-- Chọn đối tượng --</option>
                        <option value="Sinh viên" {{ old('doi_tuong') == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                        <option value="Giảng viên" {{ old('doi_tuong') == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                        <option value="Tất cả" {{ old('doi_tuong') == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>

                {{-- Khu --}}
                <div class="col-md-6">
                    <label class="form-label">Chọn khu (có thể chọn nhiều)</label>
                    <select name="khu_id[]" id="khu_id" class="form-select" multiple>
                        @foreach($khus as $khu)
                        <option value="{{ $khu->id }}" {{ collect(old('khu_id'))->contains($khu->id) ? 'selected' : '' }}>
                            {{ $khu->ten_khu }}
                        </option>
                        @endforeach
                    </select>
                    <small class="nf-hint-text">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều khu.</small>
                </div>

                {{-- Phòng --}}
                <div class="col-md-6">
                    <label class="form-label">Chọn phòng (có thể chọn nhiều)</label>
                    <select name="phong_id[]" id="phong_id" class="form-select" multiple>
                        @foreach($phongs as $phong)
                        <option value="{{ $phong->id }}" {{ collect(old('phong_id'))->contains($phong->id) ? 'selected' : '' }}>
                            {{ $phong->ten_phong }} ({{ $phong->khu->ten_khu ?? '' }})
                        </option>
                        @endforeach
                    </select>
                    <small class="nf-hint-text">Chỉ chọn khi cần gửi chính xác đến từng phòng cụ thể.</small>
                </div>
            </div>
        </div>

        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Tệp đính kèm</p>
                    <h5 class="nf-section-title">Hình ảnh & tài liệu liên quan</h5>
                </div>
            </div>
            <div class="row g-4">
                {{-- Ảnh --}}
                <div class="col-md-6">
                    <label class="form-label">Ảnh thông báo</label>
                    <div class="nf-attachment-box">
                        <input type="file" name="anh" class="form-control" accept="image/*">
                        <small class="nf-hint-text">PNG, JPG hoặc WEBP, tối đa 3MB.</small>
                    </div>
                </div>

                {{-- File đính kèm --}}
                <div class="col-md-6">
                    <label class="form-label">File đính kèm</label>
                    <div class="nf-attachment-box">
                        <input type="file" name="file" class="form-control" accept=".doc,.docx,.pdf,.xls,.xlsx">
                        <small class="nf-hint-text">Cho phép chia sẻ biểu mẫu, kế hoạch, thông báo chính thức.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="nf-form-actions">
            <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Hủy</a>
            <button type="submit" class="btn btn-success px-4">Lưu thông báo</button>
        </div>
    </form>
</div>
    @endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ----------------- TIÊU ĐỀ -----------------
    const tieuDeSelect = document.getElementById('tieu_de_id');
    const addTitleBtn = document.getElementById('add_title_btn');
    const deleteTitleBtn = document.getElementById('delete_title_btn');
    const inputTieuDe = document.getElementById('input_tieu_de');
    const tieudeCreateUrl = "{{ route('tieude.ajaxCreate') }}";
    const tieudeDeleteUrl = "{{ route('tieude.ajaxDelete') }}";

    addTitleBtn.addEventListener('click', () => {
        inputTieuDe.style.display = 'block';
        inputTieuDe.focus();
    });

    inputTieuDe.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = inputTieuDe.value.trim();
            if (!value) return;

            fetch(tieudeCreateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ten_tieu_de: value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.text = data.ten_tieu_de;
                    option.selected = true;
                    tieuDeSelect.appendChild(option);
                    inputTieuDe.value = '';
                    inputTieuDe.style.display = 'none';
                    alert('✅ Thêm tiêu đề thành công');
                } else {
                    alert('❌ Không thể thêm tiêu đề');
                }
            })
            .catch(err => {
                console.error(err);
                alert('⚠️ Lỗi khi thêm tiêu đề');
            });
        }
    });

    deleteTitleBtn.addEventListener('click', () => {
        const selected = tieuDeSelect.value;
        if (!selected) return alert('Chọn tiêu đề để xóa');
        if (!confirm('Bạn có chắc chắn muốn xóa không?')) return;

        fetch(tieudeDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: selected })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                tieuDeSelect.querySelector(`option[value="${selected}"]`).remove();
                alert('🗑️ Xóa tiêu đề thành công');
            } else {
                alert('⚠️ Không thể xóa tiêu đề (đang được sử dụng)');
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Lỗi khi xóa tiêu đề');
        });
    });

    // ================== MỨC ĐỘ ƯU TIÊN ==================
    const prioritySelect = document.getElementById('muc_do_id');
    const addPriorityBtn = document.getElementById('add_priority_btn');
    const deletePriorityBtn = document.getElementById('delete_priority_btn');
    const inputPriority = document.getElementById('input_muc_do');
    const mucdoCreateUrl = "{{ route('mucdo.ajaxCreate') }}";
    const mucdoDeleteUrl = "{{ route('mucdo.ajaxDelete') }}";

    addPriorityBtn.addEventListener('click', () => {
        inputPriority.style.display = 'block';
        inputPriority.focus();
    });

    inputPriority.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = inputPriority.value.trim();
            if (!value) return;

            fetch(mucdoCreateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ten_muc_do: value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.text = data.ten_muc_do;
                    option.selected = true;
                    prioritySelect.appendChild(option);
                    inputPriority.value = '';
                    inputPriority.style.display = 'none';
                    alert('✅ Thêm mức độ thành công');
                } else {
                    alert('❌ Không thể thêm mức độ');
                }
            })
            .catch(err => {
                console.error(err);
                alert('⚠️ Lỗi khi thêm mức độ');
            });
        }
    });

    deletePriorityBtn.addEventListener('click', () => {
        const selected = prioritySelect.value;
        if (!selected) return alert('Chọn mức độ để xóa');
        if (!confirm('Bạn có chắc chắn muốn xóa không?')) return;

        fetch(mucdoDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: selected })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                prioritySelect.querySelector(`option[value="${selected}"]`).remove();
                alert('🗑️ Xóa mức độ thành công');
            } else {
                alert('⚠️ Không thể xóa mức độ (đang được sử dụng)');
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Lỗi khi xóa mức độ');
        });
    });
     // Kích hoạt Select2 cho khu
        $('#khu_id').select2({
            placeholder: '🔍 Chọn khu',
            allowClear: true,
            width: '100%'
        });

        // Kích hoạt Select2 cho phòng
        $('#phong_id').select2({
            placeholder: '🏠 Chọn phòng',
            allowClear: true,
            width: '100%'
        });

        // ==================== AlertifyJS cấu hình ====================
        alertify.set('notifier', 'position', 'top-right');
        alertify.defaults.theme.ok = "btn btn-success";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";

        // Thông báo khi chọn KHU
        $('#khu_id').on('select2:select', function(e) {
            var data = e.params.data;
            alertify.success(`✅ Đã chọn khu: <b>${data.text}</b>`);
        });

        $('#khu_id').on('select2:unselect', function(e) {
            var data = e.params.data;
            alertify.message(`❎ Bỏ chọn khu: <b>${data.text}</b>`);
        });

        // Thông báo khi chọn PHÒNG
        $('#phong_id').on('select2:select', function(e) {
            var data = e.params.data;
            alertify.success(`✅ Đã chọn phòng: <b>${data.text}</b>`);
        });

        $('#phong_id').on('select2:unselect', function(e) {
            var data = e.params.data;
            alertify.message(`❎ Bỏ chọn phòng: <b>${data.text}</b>`);
        });

    });
</script>
@endpush
@push('styles')
@include('thongbao.partials.form-styles')
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- AlertifyJS -->
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
@endpush

@push('scripts')
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- AlertifyJS -->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
@endpush
@include('components.ckeditor', [
    'selector' => '#noi_dung',
    'form' => '#thongbao-form',
    'editorVar' => 'thongBaoEditor',
])
