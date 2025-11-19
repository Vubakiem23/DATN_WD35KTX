<?php

namespace App\Traits;

use App\Models\HoaDon;
use App\Models\HoaDonSlotPayment;
use App\Models\Phong;

trait HoaDonCalculations
{
    /**
     * Lấy thông tin đơn giá/slot và tiền phòng của phòng
     */
    protected function getPhongPricing(?Phong $phong): array
    {
        if (!$phong) {
            return [
                'slot_unit_price' => 0,
                'slot_count' => 0,
                'tien_phong' => 0,
            ];
        }

        $slotUnitPrice = $phong->giaSlot();
        $occupiedSlotCount = $phong->billableSlotCount(true);

        return [
            'slot_unit_price' => $slotUnitPrice,
            'slot_count' => $occupiedSlotCount,
            'tien_phong' => $slotUnitPrice * $occupiedSlotCount,
        ];
    }

    /**
     * Gắn thông tin tiền phòng slot vào đối tượng hóa đơn
     */
    protected function enrichHoaDonWithPhongPricing(HoaDon $hoaDon): HoaDon
    {
        $pricing = $this->getPhongPricing($hoaDon->phong);

        $hoaDon->tien_phong_slot = $pricing['tien_phong'];
        $hoaDon->slot_unit_price = $pricing['slot_unit_price'];
        $hoaDon->slot_billing_count = $pricing['slot_count'];

        return $hoaDon;
    }

    /**
     * Gắn thông tin phân bổ chi phí theo slot vào hóa đơn
     */
    protected function attachSlotBreakdown(HoaDon $hoaDon): HoaDon
    {
        if (!$hoaDon->relationLoaded('phong')) {
            $hoaDon->load('phong');
        }

        if ($hoaDon->phong) {
            $hoaDon->phong->loadMissing('slots.sinhVien');
        }

        $hoaDon->slot_breakdowns = $this->buildSlotBreakdown($hoaDon);

        return $hoaDon;
    }

    /**
     * Khởi tạo slot payments nếu chưa có
     */
    protected function initializeSlotPayments(HoaDon $hoaDon): void
    {
        if (!$hoaDon->slot_breakdowns || empty($hoaDon->slot_breakdowns)) {
            return;
        }

        $existingCount = $hoaDon->slotPayments()->count();
        if ($existingCount > 0) {
            return;
        }

        foreach ($hoaDon->slot_breakdowns as $breakdown) {
            $slot = null;
            if ($hoaDon->phong && $hoaDon->phong->slots) {
                $slot = $hoaDon->phong->slots->firstWhere('ma_slot', $breakdown['label']);
            }

            HoaDonSlotPayment::create([
                'hoa_don_id' => $hoaDon->id,
                'slot_id' => $slot?->id,
                'slot_label' => $breakdown['label'],
                'sinh_vien_id' => $slot?->sinh_vien_id,
                'sinh_vien_ten' => $breakdown['sinh_vien'] ?? null,
                'trang_thai' => HoaDonSlotPayment::TRANG_THAI_CHUA_THANH_TOAN,
                'da_thanh_toan' => false,
            ]);
        }
    }

    /**
     * Tạo dữ liệu phân bổ chi phí điện/nước/phòng cho từng slot
     */
    protected function buildSlotBreakdown(HoaDon $hoaDon): array
    {
        $phong = $hoaDon->phong;
        if (!$phong) {
            return [];
        }

        $slots = $phong->slots
            ->filter(function ($slot) {
                return !is_null($slot->sinh_vien_id) || $slot->sinhVien;
            })
            ->sortBy(function ($slot) {
                return $slot->ma_slot ?? $slot->id;
            })
            ->values();

        $slotCount = (int) ($hoaDon->slot_billing_count ?? $slots->count());
        if ($slotCount <= 0) {
            return [];
        }

        $dienShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_dien ?? 0));
        $nuocShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_nuoc ?? 0));
        $phongShares = $this->splitAmountAcrossSlots($slotCount, (int) round($hoaDon->tien_phong_slot ?? 0));

        $breakdowns = [];
        for ($i = 0; $i < $slotCount; $i++) {
            $slot = $slots->get($i);
            $label = $slot ? ($slot->ma_slot ?? 'Slot ' . ($i + 1)) : 'Slot ' . ($i + 1);

            $breakdowns[] = [
                'label' => $label,
                'sinh_vien' => optional($slot?->sinhVien)->ho_ten ?? 'Chưa có sinh viên',
                'tien_dien' => $dienShares[$i] ?? 0,
                'tien_nuoc' => $nuocShares[$i] ?? 0,
                'tien_phong' => $phongShares[$i] ?? 0,
            ];
        }

        return $breakdowns;
    }

    /**
     * Chia đều số tiền cho từng slot và xử lý phần dư để đảm bảo tổng chính xác
     */
    protected function splitAmountAcrossSlots(int $slotCount, int $total): array
    {
        if ($slotCount <= 0) {
            return [];
        }

        $base = intdiv($total, $slotCount);
        $remainder = $total - ($base * $slotCount);

        $shares = array_fill(0, $slotCount, $base);
        for ($i = 0; $i < $remainder; $i++) {
            $shares[$i] += 1;
        }

        return $shares;
    }
}

