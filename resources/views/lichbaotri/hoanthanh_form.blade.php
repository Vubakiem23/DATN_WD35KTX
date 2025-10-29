<form action="{{ route('lichbaotri.hoanthanh.submit', $lich->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="hinh_anh_sau">Ảnh sau bảo trì (minh chứng)</label>
        <input type="file" name="hinh_anh_sau" id="hinh_anh_sau" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="ngay_hoan_thanh">Ngày hoàn thành</label>
        <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" 
               value="{{ now()->toDateString() }}" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="mo_ta">Mô tả (tùy chọn)</label>
        <textarea name="mo_ta" id="mo_ta" class="form-control" rows="3">{{ old('mo_ta', $lich->mo_ta) }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary mt-3">✅ Hoàn thành</button>
</form>
