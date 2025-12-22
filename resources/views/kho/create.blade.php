@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')

@section('content')
<style>
    /* --- Card và tổng thể --- */
    .card {
        border-radius: 20px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
        border: none;
        padding: 30px;
        background-color: #ffffff;
    }

    /* --- Tiêu đề --- */
    .bulk-title {
        font-weight: 800;
        color: #1f2937;
        font-size: 1.25rem;
        margin-bottom: 20px;
    }

    /* --- Bảng --- */
    .bulk-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .bulk-table thead th {
        background: #f1f5f9;
        padding: 12px 15px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #475569;
        border-bottom: none;
        border-radius: 12px;
    }

    .bulk-table tbody tr {
        background: #ffffff;
        transition: all 0.2s;
        border-radius: 12px;
    }

    .bulk-table tbody tr:hover {
        background: #f0f4f8;
    }

    .bulk-table tbody td {
        padding: 10px 12px;
        vertical-align: middle;
    }

    /* --- Inputs & selects --- */
    .form-control,
    .form-select {
        border-radius: 12px;
        height: 42px;
        border-color: #cbd5e1;
        padding: 5px 12px;
        font-size: 14px;
    }

    textarea.form-control {
        min-height: 70px;
        resize: vertical;
    }

    /* --- Preview ảnh --- */
    .img-preview {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        object-fit: cover;
        background: #f1f5f9;
    }

    /* --- Dòng trống --- */
    .empty-state td {
        text-align: center;
        padding: 25px;
        font-style: italic;
        color: #94a3b8;
    }

    /* --- Cột xoá --- */
    .bulk-table td.actions-delete {
        white-space: nowrap;
    }

    .bulk-table td.actions-delete .btn-sm {
        padding: 6px 10px;
        font-size: 13px;
        border-radius: 999px;
    }

    .bulk-table td.actions-delete .btn-sm {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* --- Nút thêm dòng --- */
    #addRow {
        border-radius: 999px;
    }

    /* --- Responsive table --- */
    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="container mt-4">
    <h4 class="bulk-title">➕ Thêm nhiều tài sản cho loại: {{ $loai->ten_loai }}</h4>

    <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data" class="card">
        @csrf

        <div class="table-responsive">
            <table class="table bulk-table" id="assetTable">
                <thead>
                    <tr>
                        <th>Tên tài sản</th>
                        <th>Đơn vị</th>
                        <th>Tình trạng</th>
                        <th>Ghi chú</th>
                        <th>Hình ảnh</th>
                        <th>Xem trước</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">Xoá</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <button type="button" id="addRow" class="btn btn-dergin btn-dergin--info mt-3">
            ➕ Thêm dòng
        </button>

        <div class="text-end mt-4">
            <button class="btn btn-primary">💾 Lưu tất cả</button>
        </div>
    </form>
</div>

<script>
    const tbody = document.querySelector('#assetTable tbody');
    const addRowBtn = document.getElementById('addRow');

    const tenDefault = @json($loai - > ten_loai);
    const tinhTrangOptions = @json($tinhTrangOptions);

    function buildTinhTrangOptions(selected = "") {
        return `
        <option value="">--Chọn--</option>
        ${tinhTrangOptions.map(x => `<option value="${x}" ${x===selected?"selected":""}>${x}</option>`).join("")}
    `;
    }

    function createRow(copyData = null, copiedFile = null) {
        const tr = document.createElement('tr');
        const don_vi_value = copyData?.don_vi ?? "";
        const tinh_trang_value = copyData?.tinh_trang ?? "";
        const ghi_chu_value = copyData?.ghi_chu ?? "";
        const img_src_value = copyData?.img_src ?? "";
        tr.innerHTML = `
        <td><input type="text" name="ten_tai_san[]" class="form-control" value="${tenDefault}" readonly></td>
        <td><input type="text" name="don_vi_tinh[]" class="form-control" value="${don_vi_value}" placeholder="chiếc, bộ..."></td>
        <td><select name="tinh_trang[]" class="form-select">${buildTinhTrangOptions(tinh_trang_value)}</select></td>
        <td><textarea name="ghi_chu[]" class="form-control" placeholder="Ghi chú thêm...">${ghi_chu_value}</textarea></td>
        <td><input type="file" name="hinh_anh[]" class="form-control file-input" accept="image/*"></td>
        <td class="text-center"><img class="img-preview" src="${img_src_value}"></td>
        <td class="text-center">
            <input type="number" class="form-control" name="so_luong[]" min="1" value="1">
        </td>
        <td class="text-center actions-delete">
            <button type="button" class="btn btn-danger btn-sm remove-row" title="Xoá dòng">
                ✖
            </button>
        </td>
    `;

        if (copiedFile) {
            const fileInput = tr.querySelector(".file-input");
            const dt = new DataTransfer();
            dt.items.add(copiedFile);
            fileInput.files = dt.files;
        }
        return tr;
    }

    function showEmptyState() {
        if (tbody.children.length === 0) {
            const tr = document.createElement('tr');
            tr.classList.add('empty-state');
            tr.innerHTML = `<td colspan="8">Chưa có dòng nào. Nhấn <b>Thêm dòng</b> để bắt đầu.</td>`;
            tbody.appendChild(tr);
        }
    }

    function removeEmptyState() {
        const empty = tbody.querySelector('.empty-state');
        if (empty) empty.remove();
    }

    // Thêm dòng mới
    addRowBtn.addEventListener('click', () => {
        removeEmptyState();
        tbody.appendChild(createRow());
    });

    // Xoá dòng
    document.addEventListener('click', e => {
        const row = e.target.closest('tr');
        if (e.target.classList.contains('remove-row')) {
            row.remove();
            showEmptyState();
        }
    });

    // Preview ảnh
    document.addEventListener('change', e => {
        if (e.target.classList.contains('file-input')) {
            const img = e.target.closest('tr').querySelector('.img-preview');
            const file = e.target.files[0];
            img.src = file ? URL.createObjectURL(file) : "";
        }
    });

    // Khởi tạo 1 dòng mặc định
    removeEmptyState();
    tbody.appendChild(createRow());
</script>

@endsection