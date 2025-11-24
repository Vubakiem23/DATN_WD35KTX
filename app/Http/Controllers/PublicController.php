<?php

namespace App\Http\Controllers;

use App\Models\ThongBao;
use App\Models\TinTuc;
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\Khu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    /**
     * Trang chủ công khai
     */
    public function home()
{
    try {
        // Lấy 5 thông báo mới nhất
        $thongBaos = ThongBao::orderBy('ngay_dang', 'desc')
            ->limit(5)
            ->get();

        // Lấy 3 tin tức mới nhất
        $tinTuc = TinTuc::with('hashtags')
            ->orderBy('ngay_tao', 'desc')
            ->limit(10)
            ->get();

        // Quyền hiển thị nút đăng ký
        $canRegister = true;
        $registerMessage = null;
        $hideRegisterButton = false;

        if (Auth::check()) {
            $existing = SinhVien::where('user_id', Auth::id())->first();
            if ($existing) {
                $canRegister = false;
                if ($existing->trang_thai_ho_so === SinhVien::STATUS_APPROVED) {
                    $registerMessage = 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.';
                    $hideRegisterButton = true;
                } elseif ($existing->trang_thai_ho_so === SinhVien::STATUS_PENDING_CONFIRMATION) {
                    $registerMessage = 'Hồ sơ đã được duyệt. Vui lòng xác nhận để hoàn tất thủ tục.';
                    $hideRegisterButton = true;
                } else {
                    $registerMessage = 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.';
                    $hideRegisterButton = false;
                }
            }
        }
    } catch (\Exception $e) {
        // Nếu có lỗi (ví dụ: chưa có dữ liệu), trả về collection rỗng
        $thongBaos = collect([]);
        $tinTuc = collect([]);
        $canRegister = true;
        $registerMessage = null;
        $hideRegisterButton = false;
    }

    return view('public.home', compact(
        'thongBaos',
        'tinTuc',
        'canRegister',
        'registerMessage',
        'hideRegisterButton'
    ));
}


    /**
     * Form đăng ký ký túc xá (Public)
     */
    public function applyForm()
    {
        // Chặn đăng ký khi đã có hồ sơ
        if (Auth::check()) {
            $existing = SinhVien::where('user_id', Auth::id())->first();
            if ($existing) {
                if ($existing->trang_thai_ho_so === SinhVien::STATUS_APPROVED) {
                    return redirect()->route('client.dashboard')
                        ->with('warning', 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.');
                }
                if ($existing->trang_thai_ho_so === SinhVien::STATUS_PENDING_CONFIRMATION) {
                    return redirect()->route('client.confirmation.show')
                        ->with('warning', 'Hồ sơ đã được duyệt. Vui lòng xác nhận trước khi đăng ký thêm.');
                }
                return redirect()->route('public.home')
                    ->with('warning', 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.');
            }
        }
        return view('public.apply');
    }

    /**
     * Lưu hồ sơ sinh viên ở trạng thái 'Chờ duyệt'
     */
    public function applyStore(Request $request)
    {
        // Nếu đã đăng nhập và đã có hồ sơ thì chặn submit
        if (Auth::check()) {
            $existing = SinhVien::where('user_id', Auth::id())->first();
            if ($existing) {
                if ($existing->trang_thai_ho_so === SinhVien::STATUS_APPROVED) {
                    return redirect()->route('client.dashboard')
                        ->with('warning', 'Hồ sơ đã được duyệt. Bạn không thể đăng ký thêm.');
                }
                if ($existing->trang_thai_ho_so === SinhVien::STATUS_PENDING_CONFIRMATION) {
                    return redirect()->route('client.confirmation.show')
                        ->with('warning', 'Hồ sơ đã được duyệt. Vui lòng xác nhận trước khi đăng ký thêm.');
                }
                return redirect()->route('public.home')
                    ->with('warning', 'Bạn đã gửi hồ sơ. Vui lòng chờ duyệt.');
            }
        }

        $data = $request->validate([
            'ma_sinh_vien' => 'required|string|unique:sinh_vien,ma_sinh_vien',
            'ho_ten' => 'required|string',
            'ngay_sinh' => 'required|date',
            'gioi_tinh' => 'required|string',
            'que_quan' => 'required|string',
            'noi_o_hien_tai' => 'required|string',
            'lop' => 'required|string',
            'nganh' => 'required|string',
            'khoa_hoc' => 'required|string',
            'so_dien_thoai' => 'required|string',
            'email' => 'required|email',
            'anh_sinh_vien' => 'nullable|image|max:2048',
            'citizen_id_number' => 'nullable|string',
            'citizen_issue_date' => 'nullable|date',
            'citizen_issue_place' => 'nullable|string',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'guardian_relationship' => 'nullable|string',
        ]);

        // Nếu đã đăng nhập, luôn dùng email tài khoản và gắn user_id
        if (Auth::check()) {
            $data['email'] = Auth::user()->email;
            $data['user_id'] = Auth::id();
        }

        if ($request->hasFile('anh_sinh_vien')) {
            $data['anh_sinh_vien'] = $request->file('anh_sinh_vien')->store('students', 'public');
        }

        $data['trang_thai_ho_so'] = SinhVien::STATUS_PENDING_APPROVAL;
        $data['phong_id'] = null;

        SinhVien::create($data);

        return redirect()->route('public.home')->with('success', 'Đã gửi hồ sơ. Vui lòng chờ duyệt.');
    }

    /**
     * Trang giới thiệu chung ký túc xá
     */
    public function about()
    {
        $stats = [
            'rooms' => Phong::count(),
            'areas' => Khu::count(),
            'students' => SinhVien::count(),
            'activities' => 12,
        ];

        $history = [
            [
                'year' => '2018',
                'title' => 'Khởi công xây dựng',
                'description' => 'Ký túc xá VaMos được khởi công xây dựng với mục tiêu tạo không gian sống hiện đại, an toàn cho sinh viên FPT Polytechnic.'
            ],
            [
                'year' => '2019',
                'title' => 'Hoàn thành giai đoạn 1',
                'description' => 'Khu A và Khu B được hoàn thành và đưa vào sử dụng, đón nhận hơn 500 sinh viên đầu tiên.'
            ],
            [
                'year' => '2020',
                'title' => 'Mở rộng quy mô',
                'description' => 'Hoàn thành Khu C và Khu D, nâng tổng số phòng lên hơn 1.200 phòng, đáp ứng nhu cầu của hơn 2.400 sinh viên.'
            ],
            [
                'year' => '2021',
                'title' => 'Ứng dụng công nghệ',
                'description' => 'Triển khai hệ thống quản lý thông minh, ứng dụng công nghệ vào quản lý và vận hành ký túc xá.'
            ],
            [
                'year' => '2022 - Nay',
                'title' => 'Phát triển bền vững',
                'description' => 'Không ngừng cải thiện chất lượng dịch vụ, mở rộng hoạt động cộng đồng và nâng cao trải nghiệm sinh viên.'
            ],
        ];

        $highlights = [
            [
                'icon' => 'fa-shield-alt',
                'title' => 'An toàn & bảo mật',
                'description' => 'Hệ thống kiểm soát ra vào thông minh, trực bảo vệ 24/7 và camera giám sát toàn khu vực đảm bảo an ninh tuyệt đối.'
            ],
            [
                'icon' => 'fa-wifi',
                'title' => 'Tiện ích đầy đủ',
                'description' => 'Wifi tốc độ cao, phòng sinh hoạt chung, khu tự học, căn tin và các tiện ích hiện đại phục vụ nhu cầu học tập và sinh hoạt.'
            ],
            [
                'icon' => 'fa-hands-helping',
                'title' => 'Cộng đồng gắn kết',
                'description' => 'Hơn 20 câu lạc bộ đa dạng, chương trình mentoring, hoạt động ngoại khóa và sự kiện cộng đồng mỗi tháng.'
            ],
            [
                'icon' => 'fa-building',
                'title' => 'Cơ sở vật chất hiện đại',
                'description' => 'Phòng ở tiện nghi, không gian học tập rộng rãi, khu thể thao và các tiện ích công cộng được đầu tư bài bản.'
            ],
            [
                'icon' => 'fa-users',
                'title' => 'Đội ngũ chuyên nghiệp',
                'description' => 'Ban quản lý tận tâm, đội ngũ hỗ trợ 24/7 luôn sẵn sàng giải đáp và hỗ trợ sinh viên mọi lúc, mọi nơi.'
            ],
            [
                'icon' => 'fa-leaf',
                'title' => 'Môi trường xanh',
                'description' => 'Không gian xanh, sạch, đẹp với hệ thống xử lý rác thải thân thiện môi trường và văn hóa sống xanh.'
            ],
        ];

        $values = [
            'Kỷ luật – Văn minh – Sáng tạo',
            'Sống xanh – Sống sạch – Sống trách nhiệm',
            'Sẵn sàng hỗ trợ và sẻ chia cùng nhau',
            'Tôn trọng sự đa dạng và bình đẳng',
            'Phát triển toàn diện về học tập và kỹ năng sống',
        ];

        $guideSteps = [
            'Đăng ký hồ sơ trực tuyến tại mục Đăng ký ký túc xá trên website.',
            'Xác nhận email và bổ sung đầy đủ giấy tờ theo hướng dẫn của Ban quản lý.',
            'Nhận kết quả xét duyệt qua email, hoàn tất phí nội trú và ký hợp đồng.',
            'Nhận phòng, bàn giao tài sản và làm thẻ ra vào ký túc xá.',
        ];

        return view('public.about', compact('stats', 'history', 'highlights', 'values', 'guideSteps'));
    }

    /**
     * Trang hướng dẫn thủ tục
     */
    public function guide()
    {
        $guideSteps = [
            [
                'number' => 1,
                'title' => 'Đăng ký hồ sơ trực tuyến',
                'description' => 'Truy cập mục "Đăng ký ký túc xá" trên website, điền đầy đủ thông tin cá nhân và tải lên các giấy tờ cần thiết.',
                'icon' => 'fa-file-alt'
            ],
            [
                'number' => 2,
                'title' => 'Xác nhận và bổ sung giấy tờ',
                'description' => 'Xác nhận email đăng ký và bổ sung đầy đủ giấy tờ theo hướng dẫn của Ban quản lý (CMND/CCCD, giấy khai sinh, ảnh thẻ...).',
                'icon' => 'fa-check-circle'
            ],
            [
                'number' => 3,
                'title' => 'Nhận kết quả xét duyệt',
                'description' => 'Nhận kết quả xét duyệt qua tài khoản hoặc nhận điện thoại xác nhận của tổng đài, hoàn tất phí nội trú và xác nhận theo hướng dẫn.',
                'icon' => 'fa-envelope'
            ],
            [
                'number' => 4,
                'title' => 'Nhận phòng và làm thủ tục',
                'description' => 'Nhận phòng, bàn giao tài sản và làm thẻ ra vào ký túc xá tại văn phòng quản lý.',
                'icon' => 'fa-key'
            ],
        ];

        $documents = [
            'CMND/CCCD (bản sao có công chứng)',
            'Giấy khai sinh (bản sao)',
            'Ảnh thẻ 3x4 (2 tấm)',
            'Giấy xác nhận học tập tại trường',
            'Giấy khám sức khỏe (nếu có)',
        ];

        $fees = [
            [
                'title' => 'Phí đăng ký',
                'amount' => 'Miễn phí',
                'description' => 'Không mất phí khi đăng ký hồ sơ'
            ],
            [
                'title' => 'Phí nội trú',
                'amount' => 'Theo quy định',
                'description' => 'Thanh toán sau khi được duyệt hồ sơ'
            ],
            [
                'title' => 'Phí bảo hiểm',
                'amount' => 'Theo quy định',
                'description' => 'Bảo hiểm y tế và bảo hiểm tài sản'
            ],
        ];

        return view('public.guide', compact('guideSteps', 'documents', 'fees'));
    }

    /**
     * Trang nội quy ký túc xá
     */
    public function rules()
    {
        $generalRules = [
            [
                'title' => 'Giờ giấc',
                'icon' => 'fa-clock',
                'items' => [
                    'Giờ tắt đèn: 22h30 hàng ngày (từ Chủ nhật đến Thứ 5)',
                    'Giờ tắt đèn: 23h00 (Thứ 6 và Thứ 7)',
                    'Giờ mở cửa: 5h30 sáng',
                    'Giờ đóng cửa: 22h00 (từ Chủ nhật đến Thứ 5), 23h00 (Thứ 6, Thứ 7)',
                    'Nghiêm cấm ra ngoài sau giờ đóng cửa nếu không có lý do chính đáng',
                ]
            ],
            [
                'title' => 'An ninh & An toàn',
                'icon' => 'fa-shield-alt',
                'items' => [
                    'Luôn mang theo thẻ sinh viên khi ra vào ký túc xá',
                    'Không được cho người lạ vào phòng, khu vực ký túc xá',
                    'Báo ngay cho bảo vệ khi phát hiện người lạ hoặc hành vi đáng nghi',
                    'Nghiêm cấm sử dụng vũ khí, chất nổ, chất dễ cháy nổ',
                    'Không được tự ý thay đổi khóa cửa, lắp đặt thiết bị điện không được phép',
                ]
            ],
            [
                'title' => 'Vệ sinh & Môi trường',
                'icon' => 'fa-broom',
                'items' => [
                    'Giữ gìn vệ sinh chung trong phòng và khu vực công cộng',
                    'Phân loại rác thải đúng quy định (rác tái chế, rác thải thông thường)',
                    'Không vứt rác bừa bãi, đổ rác đúng nơi quy định',
                    'Vệ sinh phòng ở hàng tuần, tham gia vệ sinh khu vực chung theo lịch',
                    'Nghiêm cấm nuôi động vật trong ký túc xá',
                ]
            ],
            [
                'title' => 'Sử dụng điện & Nước',
                'icon' => 'fa-bolt',
                'items' => [
                    'Tiết kiệm điện, nước, tắt các thiết bị khi không sử dụng',
                    'Không được tự ý lắp đặt, sửa chữa hệ thống điện, nước',
                    'Báo ngay cho quản lý khi phát hiện sự cố về điện, nước',
                    'Nghiêm cấm sử dụng bếp điện, bếp gas trong phòng',
                    'Chỉ được sử dụng các thiết bị điện được phép (quạt, đèn, laptop...)',
                ]
            ],
            [
                'title' => 'Ứng xử & Văn hóa',
                'icon' => 'fa-users',
                'items' => [
                    'Tôn trọng quyền riêng tư của người khác, không làm ồn sau 22h',
                    'Không hút thuốc, uống rượu bia, sử dụng chất kích thích trong ký túc xá',
                    'Không đánh nhau, gây gổ, có hành vi bạo lực',
                    'Tôn trọng tài sản chung, không làm hư hỏng, phá hoại',
                    'Tham gia tích cực các hoạt động cộng đồng, văn hóa do ký túc xá tổ chức',
                ]
            ],
            [
                'title' => 'Tài sản & Thiết bị',
                'icon' => 'fa-couch',
                'items' => [
                    'Bảo quản tốt tài sản được cấp phát (giường, tủ, bàn ghế...)',
                    'Báo ngay khi phát hiện tài sản bị hư hỏng, mất mát',
                    'Không được tự ý di chuyển, thay đổi vị trí tài sản chung',
                    'Chịu trách nhiệm bồi thường nếu làm hư hỏng, mất mát tài sản',
                    'Khi chuyển phòng hoặc ra khỏi ký túc xá phải bàn giao đầy đủ tài sản',
                ]
            ],
        ];

        $violations = [
            [
                'level' => 'Cảnh cáo',
                'color' => 'warning',
                'items' => [
                    'Vi phạm giờ giấc lần đầu',
                    'Làm ồn sau giờ quy định',
                    'Vệ sinh phòng không đạt yêu cầu',
                    'Không tham gia vệ sinh khu vực chung',
                ]
            ],
            [
                'level' => 'Cảnh cáo & Phạt tiền',
                'color' => 'danger',
                'items' => [
                    'Vi phạm giờ giấc nhiều lần',
                    'Làm mất, hư hỏng tài sản',
                    'Sử dụng thiết bị điện không được phép',
                    'Cho người lạ vào phòng',
                    'Hút thuốc, uống rượu bia trong ký túc xá',
                ]
            ],
            [
                'level' => 'Đình chỉ ở & Buộc ra khỏi KTX',
                'color' => 'critical',
                'items' => [
                    'Đánh nhau, gây gổ, có hành vi bạo lực',
                    'Sử dụng chất kích thích, ma túy',
                    'Trộm cắp tài sản',
                    'Vi phạm nghiêm trọng nhiều lần sau khi đã được cảnh cáo',
                    'Có hành vi vi phạm pháp luật',
                ]
            ],
        ];

        $rights = [
            'Được cung cấp chỗ ở an toàn, sạch sẽ',
            'Được sử dụng các tiện ích công cộng (wifi, phòng sinh hoạt, khu thể thao...)',
            'Được tham gia các hoạt động văn hóa, thể thao do ký túc xá tổ chức',
            'Được bảo vệ quyền lợi và an toàn cá nhân',
            'Được khiếu nại, đề xuất ý kiến với Ban quản lý',
            'Được thông báo về các quy định, thay đổi liên quan đến ký túc xá',
        ];

        $responsibilities = [
            'Tuân thủ nghiêm chỉnh nội quy ký túc xá',
            'Tham gia đầy đủ các buổi họp, hoạt động bắt buộc',
            'Thanh toán đầy đủ, đúng hạn các khoản phí',
            'Báo cáo kịp thời các sự cố, vấn đề phát sinh',
            'Tôn trọng và hợp tác với Ban quản lý, bảo vệ',
            'Giữ gìn hình ảnh tốt đẹp của ký túc xá và sinh viên',
        ];

        return view('public.rules', compact('generalRules', 'violations', 'rights', 'responsibilities'));
    }
}

