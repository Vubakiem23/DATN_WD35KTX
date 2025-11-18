@extends('admin.layouts.admin')

@section('title', 'Thêm tin tức')

@section('content')
<div class="notification-form-wrapper">
    <div class="mb-5">
        <h3 class="room-page__title mb-2">Thêm Tin Tức</h3>
        <p class="text-muted mb-0">Ghi lại hoạt động nổi bật, đính kèm hashtag và hình ảnh truyền thông.</p>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('tintuc.store') }}" method="POST" enctype="multipart/form-data" id="tintuc-form" class="notification-form-card">
        @csrf

        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Nội dung chính</p>
                    <h5 class="nf-section-title">Tiêu đề & câu chuyện</h5>
                </div>
                <span class="nf-chip nf-chip--subtle">Bắt buộc</span>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Nội dung</label>
                    <textarea name="noi_dung" id="noi_dung" class="form-control" rows="6">{{ old('noi_dung') }}</textarea>
                    <small class="nf-hint-text">Trình soạn thảo hỗ trợ chèn ảnh, định dạng chữ và liên kết.</small>
                </div>
            </div>
        </div>

        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Thời gian & thẻ</p>
                    <h5 class="nf-section-title">Lên lịch và nhóm nội dung</h5>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Ngày đăng</label>
                    <input type="date" name="ngay_tao" class="form-control" value="{{ old('ngay_tao', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-flex justify-content-between">
                        <span>Hashtags</span>
                        <span class="nf-hint-text mb-0">Có thể nhập mới</span>
                    </label>
                    <select name="hashtags[]" id="hashtags" class="form-select select2-tags" multiple>
                        @foreach($hashtags as $hashtag)
                        <option value="{{ $hashtag->id }}">{{ $hashtag->ten }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="nf-section">
            <div class="nf-section-header">
                <div>
                    <p class="nf-section-eyebrow">Tư liệu</p>
                    <h5 class="nf-section-title">Ảnh bìa & xem trước</h5>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh</label>
                    <div class="nf-attachment-box">
                        <input type="file" name="hinh_anh" class="form-control" accept="image/*" id="hinh_anh">
                        <small class="nf-hint-text">PNG, JPG, GIF hoặc WEBP, tối đa 2MB.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Xem trước</label>
                    <div class="nf-preview-frame">
                        <img id="preview-img" src="https://dummyimage.com/420x240/eff3f9/9aa8b8&text=Preview" alt="Preview hình ảnh">
                    </div>
                </div>
            </div>
        </div>

        <div class="nf-form-actions">
            <a href="{{ route('tintuc.index') }}" class="btn btn-outline-secondary">Hủy</a>
            <button type="submit" class="btn btn-success px-4">Đăng tin tức</button>
        </div>
    </form>
</div>
@endsection

@push('styles')
@include('thongbao.partials.form-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .nf-preview-frame {
        border: 1px dashed rgba(15, 23, 42, 0.2);
        border-radius: 18px;
        padding: 16px;
        background: #fff;
        text-align: center;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nf-preview-frame img {
        max-width: 100%;
        border-radius: 12px;
        object-fit: cover;
    }

    .select2-selection__choice {
        background-color: #2563eb !important;
        border: none !important;
        color: #fff !important;
        border-radius: 999px !important;
        padding: 4px 12px !important;
        margin-top: 6px !important;
        font-size: 13px !important;
    }

    .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 8px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#hashtags').select2({
        placeholder: "🏷️ Chọn hashtag",
        allowClear: true,
        tags: true,
        width: "100%",
    });

    const input = document.getElementById('hinh_anh');
    const preview = document.getElementById('preview-img');
    const fallback = "https://dummyimage.com/420x240/eff3f9/9aa8b8&text=Preview";

    input.addEventListener('change', function () {
        const [file] = input.files;
        preview.src = file ? URL.createObjectURL(file) : fallback;
    });
});
</script>
@endpush

@include('components.ckeditor', [
    'selector' => '#noi_dung',
    'form' => '#tintuc-form',
    'editorVar' => 'tinTucEditor',
])
