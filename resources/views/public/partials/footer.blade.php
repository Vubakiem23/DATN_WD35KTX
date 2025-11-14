<footer class="main-footer">
    <div class="container">
        <!-- Main Footer Content -->
        <div class="footer-main">
            <!-- Brand & About Section -->
            <div class="footer-col footer-brand-col">
                <div class="footer-brand">
                    <div class="brand-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Ký Túc Xá VaMos">
                    </div>
                    <div class="brand-text">
                        <h3>Ký Túc Xá VaMos</h3>
                        <p>Hệ thống quản lý ký túc xá hiện đại, tiện ích cho sinh viên</p>
                    </div>
                </div>
                <div class="social-media">
                    <a href="https://www.facebook.com/vu.ba.kiem.2025" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="mailto:ktx@tdtu.edu.vn" class="social-icon" aria-label="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="tel:0377550556" class="social-icon" aria-label="Điện thoại">
                        <i class="fas fa-phone"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4 class="footer-title">Liên kết nhanh</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('public.home') }}">Trang chủ</a></li>
                    <li><a href="{{ route('public.about') }}">Giới thiệu</a></li>
                    <li><a href="{{ route('public.home') }}#thong-bao">Thông báo</a></li>
                    <li><a href="{{ route('public.home') }}#tin-tuc">Tin tức</a></li>
                    <li><a href="{{ route('public.about') }}#huong-dan">Hướng dẫn</a></li>
                    <li><a href="{{ route('public.about') }}#noi-quy">Nội quy</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-col">
                <h4 class="footer-title">Liên hệ</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>301 Đường Thụy Phương, Quận Bắc Từ Liêm, Hà Nội</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <a href="tel:0358228048">0358228048</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:liemptph37724@fpt.edu.vn">liemptph37724@fpt.edu.vn</a>
                    </li>
                </ul>
            </div>

            <!-- Working Hours -->
            <div class="footer-col">
                <h4 class="footer-title">Giờ làm việc</h4>
                <ul class="footer-hours">
                    <li>
                        <span class="day">Thứ 2 - Thứ 6</span>
                        <span class="time">7:00 - 17:00</span>
                    </li>
                    <li>
                        <span class="day">Thứ 7</span>
                        <span class="time">7:00 - 12:00</span>
                    </li>
                    <li>
                        <span class="day">Chủ nhật</span>
                        <span class="time">Nghỉ</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} <strong>Ký Túc Xá VaMos</strong>. An Toàn - Tiện Ích - Tiết Kiệm - Hiệu Quả.</p>
            </div>
            <div class="footer-credit">
                <span>Powered by</span>
                <strong>FPT POLYTECHNIC</strong>
            </div>
        </div>
    </div>
</footer>

<style>
    /* ===== MAIN FOOTER ===== */
    .main-footer {
        background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #1a237e 100%);
        color: #fff;
        padding: 30px 0 15px;
        position: relative;
        overflow: hidden;
        border-top: 3px solid rgba(0, 102, 204, 0.3);
    }

    .main-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #0066cc 50%, transparent);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }

    /* ===== FOOTER MAIN CONTENT ===== */
    .footer-main {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1.5fr 1.3fr;
        gap: 25px;
        margin-bottom: 20px;
    }

    /* ===== BRAND COLUMN ===== */
    .footer-brand-col {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .brand-logo {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
    }

    .brand-logo:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .brand-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .brand-text h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #fff;
        letter-spacing: 0.3px;
    }

    .brand-text p {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.75);
        margin: 0;
        line-height: 1.4;
    }

    /* Social Media */
    .social-media {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .social-icon {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 13px;
    }

    .social-icon:hover {
        background: #0066cc;
        border-color: #0066cc;
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0, 102, 204, 0.4);
        color: #fff;
    }

    /* ===== FOOTER COLUMNS ===== */
    .footer-col {
        display: flex;
        flex-direction: column;
    }

    .footer-title {
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 12px 0;
        color: #fff;
        position: relative;
        padding-bottom: 8px;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 45px;
        height: 3px;
        background: linear-gradient(90deg, #0066cc, #00a8ff);
        border-radius: 2px;
    }

    /* ===== FOOTER LINKS ===== */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
        padding-left: 0;
    }

    .footer-links a::before {
        content: '→';
        position: absolute;
        left: -18px;
        opacity: 0;
        transform: translateX(-5px);
        transition: all 0.3s ease;
        color: #0066cc;
    }

    .footer-links a:hover {
        color: #fff;
        padding-left: 18px;
    }

    .footer-links a:hover::before {
        opacity: 1;
        transform: translateX(0);
    }

    /* ===== CONTACT INFO ===== */
    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .footer-contact i {
        color: #0066cc;
        font-size: 14px;
        margin-top: 2px;
        flex-shrink: 0;
        width: 16px;
        text-align: center;
    }

    .footer-contact span,
    .footer-contact a {
        color: rgba(255, 255, 255, 0.85);
        font-size: 13px;
        line-height: 1.5;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-contact a:hover {
        color: #0066cc;
    }

    /* ===== WORKING HOURS ===== */
    .footer-hours {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .footer-hours li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-hours li:last-child {
        border-bottom: none;
    }

    .footer-hours .day {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        font-size: 13px;
    }

    .footer-hours .time {
        color: #0066cc;
        font-weight: 600;
        font-size: 13px;
        background: rgba(0, 102, 204, 0.1);
        padding: 3px 8px;
        border-radius: 5px;
    }

    /* ===== FOOTER BOTTOM ===== */
    .footer-bottom {
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .footer-copyright p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 12px;
        margin: 0;
    }

    .footer-copyright strong {
        color: #fff;
        font-weight: 600;
    }

    .footer-credit {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    .footer-credit span {
        color: rgba(255, 255, 255, 0.65);
    }

    .footer-credit strong {
        color: #0066cc;
        font-weight: 700;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1200px) {
        .footer-main {
            grid-template-columns: 2fr 1fr 1fr;
            gap: 25px;
        }

        .footer-brand-col {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 992px) {
        .main-footer {
            padding: 28px 0 18px;
        }

        .footer-main {
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .footer-brand-col {
            grid-column: 1 / -1;
            text-align: center;
            align-items: center;
        }

        .brand-logo {
            margin: 0 auto;
        }

        .social-media {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .main-footer {
            padding: 25px 0 15px;
        }

        .footer-main {
            grid-template-columns: 1fr;
            gap: 22px;
        }

        .footer-brand-col {
            text-align: center;
        }

        .footer-title {
            font-size: 15px;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }

        .footer-contact li {
            flex-direction: column;
            gap: 8px;
        }

        .footer-contact i {
            margin-top: 0;
        }

        .footer-hours li {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .footer-hours .time {
            align-self: flex-end;
        }
    }

    @media (max-width: 576px) {
        .brand-text h3 {
            font-size: 16px;
        }

        .brand-text p {
            font-size: 12px;
        }

        .footer-title {
            font-size: 14px;
            margin-bottom: 14px;
        }

        .footer-links,
        .footer-contact,
        .footer-hours {
            gap: 10px;
        }
    }
</style>