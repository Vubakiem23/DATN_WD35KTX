<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Models\SuCo;
use App\Models\ThongBaoSinhVien;
use App\Models\HoaDonSlotPayment;
use App\Models\HoaDonUtilitiesPayment;
use App\Models\NotificationRead;

use App\Observers\SuCoObserver;
use App\Models\Khu;
use App\Models\Phong;
use App\Observers\KhuObserver;
use App\Observers\PhongObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bootstrap
        Paginator::useBootstrapFive();
        SuCo::observe(SuCoObserver::class);
        Khu::observe(KhuObserver::class);
        Phong::observe(PhongObserver::class);

        /**
         * -----------------------------
         *  View Composer – Navbar thông báo
         * -----------------------------
         */
        View::composer('client.partials.header', function ($view) {

            if (!Auth::check()) {
                return $view->with('unread', 0);
            }

            $user = Auth::user();
            $sinhVien = $user->sinhVien;

            if (!$sinhVien) {
                return $view->with('unread', 0);
            }

            $sinhVienId = $sinhVien->id;

            // Lấy toàn bộ dữ liệu thông báo của SV:
            $thongBaoSinhVien = ThongBaoSinhVien::where('sinh_vien_id', $sinhVienId)->get();
            $suCo = SuCo::where('sinh_vien_id', $sinhVienId)->get();
            $slot = HoaDonSlotPayment::where('sinh_vien_id', $sinhVienId)->get();
            $utilities = HoaDonUtilitiesPayment::where('sinh_vien_id', $sinhVienId)->get();

            // Tổng số tất cả thông báo
            $total = 
                $thongBaoSinhVien->count() +
                $suCo->count() +
                $slot->count() +
                $utilities->count();

            // Tổng số đã đọc
            $read = NotificationRead::where('user_id', $user->id)->count();

            // Chưa đọc
            $unread = max($total - $read, 0);

            $view->with('unread', $unread);
        });
    }
}
