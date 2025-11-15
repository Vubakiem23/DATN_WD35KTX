@extends('public.layouts.app')

@section('title', 'Hướng dẫn thủ tục | Ký túc xá VaMos')

@push('styles')
<style>
    .guide-page {
        padding: 60px 0;
    }

    .guide-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        padding: 80px 0;
        color: white;
        text-align: center;
        margin-bottom: 60px;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }

    .guide-hero h1 {
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .guide-hero p {
        font-size: 18px;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .guide-main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 24px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        margin-bottom: 40px;
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .guide-main-image:hover {
        transform: scale(1.02);
    }

    .guide-steps {
        margin: 50px 0;
    }

    .guide-step-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .guide-step-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary-blue), var(--red-accent));
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .guide-step-card:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .guide-step-card:hover::before {
        transform: scaleY(1);
    }

    .guide-step-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .guide-step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-blue), var(--red-accent));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 900;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .guide-step-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(220, 20, 60, 0.1));
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-blue);
        font-size: 24px;
    }

    .guide-step-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-900);
        margin: 0;
    }

    .guide-step-description {
        font-size: 16px;
        line-height: 1.8;
        color: var(--text-700);
        margin: 0;
    }

    .documents-section,
    .fees-section {
        margin: 50px 0;
        border-radius: 24px;
        overflow: hidden;
    }

    .documents-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }

    .documents-list li {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        padding: 20px 24px;
        border-radius: 16px;
        border-left: 4px solid var(--primary-blue);
        font-size: 15px;
        font-weight: 600;
        color: var(--text-700);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .documents-list li::before {
        content: '📄';
        font-size: 20px;
    }

    .documents-list li:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .fees-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
    }

    .fee-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        text-align: center;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .fee-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .fee-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-900);
        margin-bottom: 12px;
    }

    .fee-amount {
        font-size: 28px;
        font-weight: 900;
        color: var(--primary-blue);
        margin-bottom: 12px;
    }

    .fee-description {
        font-size: 14px;
        color: var(--text-700);
        line-height: 1.6;
    }

    .cta-section {
        background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
        border-radius: 24px;
        padding: 60px 40px;
        text-align: center;
        color: white;
        margin: 60px 0;
        overflow: hidden;
        position: relative;
    }

    .cta-section h3 {
        font-size: 32px;
        font-weight: 900;
        margin-bottom: 20px;
        color: white;
    }

    .cta-section p {
        font-size: 18px;
        opacity: 0.95;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .cta-button {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 40px;
        background: white;
        color: var(--primary-blue);
        font-size: 18px;
        font-weight: 700;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .cta-button:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        color: var(--primary-blue);
    }

    .view-more-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .view-more-link:hover {
        color: var(--dark-blue);
        gap: 12px;
    }

    .view-more-link i {
        transition: transform 0.3s ease;
    }

    .view-more-link:hover i {
        transform: translateX(4px);
    }

    @media (max-width: 768px) {
        .guide-hero {
            padding: 50px 0;
        }

        .guide-hero h1 {
            font-size: 32px;
        }

        .guide-hero p {
            font-size: 16px;
        }

        .guide-main-image {
            height: 250px;
        }

        .guide-step-card {
            padding: 24px;
        }

        .guide-step-number {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .guide-step-title {
            font-size: 20px;
        }

        .documents-list {
            grid-template-columns: 1fr;
        }

        .fees-grid {
            grid-template-columns: 1fr;
        }

        .cta-section {
            padding: 40px 24px;
        }

        .cta-section h3 {
            font-size: 24px;
        }
    }
</style>
@endpush

@section('content')
<div class="content-section guide-page">
    <div class="container">
        <!-- Hero Section -->
        <div class="guide-hero">
            <h1>HƯỚNG DẪN THỦ TỤC</h1>
            <p>Hướng dẫn chi tiết các bước đăng ký và làm thủ tục vào ký túc xá VaMos</p>
        </div>

        <!-- Main Image -->
        <div class="text-center mb-5">
            <img src="{{ asset('images/excel-2-2-e1713237797743.jpg') }}" 
                 alt="Hướng dẫn thủ tục" 
                 class="guide-main-image">
        </div>

        <!-- Guide Steps -->
        <section class="guide-steps">
            <h2 class="section-title mb-4">Các bước thực hiện</h2>
            <div class="row">
                @foreach ($guideSteps as $step)
                <div class="col-12">
                    <div class="guide-step-card">
                        <div class="guide-step-header">
                            <div class="guide-step-number">{{ $step['number'] }}</div>
                            <div class="guide-step-icon">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </div>
                            <h3 class="guide-step-title">{{ $step['title'] }}</h3>
                        </div>
                        <p class="guide-step-description">{{ $step['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Documents Section -->
        <section class="documents-section panel">
            <h3 class="section-title mb-4">Giấy tờ cần thiết</h3>
            <ul class="documents-list">
                @foreach ($documents as $document)
                <li>{{ $document }}</li>
                @endforeach
            </ul>
        </section>

        <!-- Fees Section -->
        <section class="fees-section panel">
            <h3 class="section-title mb-4">Thông tin phí</h3>
            <div class="fees-grid">
                @foreach ($fees as $fee)
                <div class="fee-card">
                    <h4 class="fee-title">{{ $fee['title'] }}</h4>
                    <div class="fee-amount">{{ $fee['amount'] }}</div>
                    <p class="fee-description">{{ $fee['description'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <h3>Sẵn sàng đăng ký?</h3>
            <p>Hãy bắt đầu hành trình của bạn tại ký túc xá VaMos ngay hôm nay!</p>
            <a href="{{ route('public.apply') }}" class="cta-button">
                <i class="fas fa-arrow-right"></i>
                Đăng ký ngay
            </a>
        </section>

    </div>
</div>
@endsection

