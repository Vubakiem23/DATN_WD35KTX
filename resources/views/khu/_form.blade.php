@csrf

<div class="mb-3">
    <label for="ten_khu" class="form-label">Tên khu <span class="text-danger">*</span></label>
    <input type="text" name="ten_khu" id="ten_khu" value="{{ old('ten_khu', $khu->ten_khu ?? '') }}" 
           class="form-control @error('ten_khu') is-invalid @enderror" 
           placeholder="Ví dụ: A, B, C... hoặc Ký túc xá 1" required>
    @error('ten_khu')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Ví dụ: A, B, C... hoặc Ký túc xá 1</small>
</div>

<div class="mb-3">
    <label for="gioi_tinh" class="form-label">Giới tính <span class="text-danger">*</span></label>
    <select name="gioi_tinh" id="gioi_tinh" class="form-select @error('gioi_tinh') is-invalid @enderror" required>
        <option value="">--Chọn--</option>
        <option value="Nam" {{ old('gioi_tinh', $khu->gioi_tinh ?? '')=='Nam'?'selected':'' }}>Nam</option>
        <option value="Nữ" {{ old('gioi_tinh', $khu->gioi_tinh ?? '')=='Nữ'?'selected':'' }}>Nữ</option>
    </select>
    @error('gioi_tinh')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="gia_moi_slot" class="form-label">
        Giá mỗi slot (VND/tháng) <span class="text-danger">*</span>
    </label>
    <input type="number"
           name="gia_moi_slot"
           id="gia_moi_slot"
           class="form-control @error('gia_moi_slot') is-invalid @enderror"
           value="{{ old('gia_moi_slot', $khu->gia_moi_slot ?? 0) }}"
           min="0"
           step="1"
           required
           placeholder="Nhập đơn giá cho 1 slot (1 sinh viên) trong khu này">
    @error('gia_moi_slot')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <small class="text-muted">Dùng để tính tiền phòng: giá mỗi slot × số slot (sức chứa) của phòng.</small>
</div>

<div class="mb-3">
    <label for="mo_ta" class="form-label">Mô tả</label>
    <textarea name="mo_ta" id="mo_ta" rows="3" 
              class="form-control @error('mo_ta') is-invalid @enderror" 
              placeholder="Nhập mô tả về khu...">{{ old('mo_ta', $khu->mo_ta ?? '') }}</textarea>
    @error('mo_ta')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn-dergin btn-dergin--success">
        <i class="{{ $submitIcon ?? 'fa fa-plus' }}"></i>
        {{ $submitLabel ?? 'Thêm mới' }}
    </button>
    <a href="{{ route('khu.index') }}" class="btn-dergin btn-dergin--muted">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</div>



