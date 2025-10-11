<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhongRequest extends FormRequest
{
    public function authorize()
    {
        // Cho local: chỉ cần authenticated, nếu muốn kiểm tra role, sửa ở đây
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'ten_phong' => 'required|string|max:255',
            'khu' => 'nullable|string|max:255',
            'loai_phong' => 'nullable|string|max:255',
            'gioi_tinh' => 'nullable|in:Nam,Nữ,Cả hai',
            'suc_chua' => 'required|integer|min:1',
            'trang_thai' => 'required|in:Trống,Đã ở,Bảo trì',
            'ghi_chu' => 'nullable|string|max:255',
        ];
    }
}
