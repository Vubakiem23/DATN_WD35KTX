<form action="{{ route('lichbaotri.hoanthanh.submit', $lich->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group mb-3">
        <label for="hinh_anh_truoc">Ảnh sau bảo trì (minh chứng)</label>
        <input type="file" name="hinh_anh_truoc" id="hinh_anh_truoc" class="form-control" accept="image/*" required>
    </div>

    <div class="form-group mb-3">
        <label for="ngay_hoan_thanh">Ngày hoàn thành</label>
        <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh"
               value="{{ now()->toDateString() }}" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label for="mo_ta_sau">Mô tả sau bảo trì</label>
        <textarea name="mo_ta_sau" id="mo_ta_sau" rows="3" class="form-control"
                  placeholder="Nhập mô tả tình trạng sau khi bảo trì..." required></textarea>
    </div>

    <button type="submit" class="btn btn-primary mt-2">✅ Hoàn thành</button>
</form>
