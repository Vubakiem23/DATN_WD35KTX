@extends('public.layouts.app')

@section('title', 'Nội quy ký túc xá | Ký túc xá VaMos')

@push('styles')
<style>
    .rules-page {
        padding: 60px 0;
    }

    .rules-hero {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        padding: 80px 0;
        color: white;
        text-align: center;
        margin-bottom: 60px;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }

    .rules-hero h1 {
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .rules-hero p {
        font-size: 18px;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .rules-main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 24px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        margin-bottom: 40px;
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .rules-main-image:hover {
        transform: scale(1.02);
    }

    .rules-section {
        margin: 50px 0;
    }

    .rule-category-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
    }

    .rule-category-card::before {
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

    .rule-category-card:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .rule-category-card:hover::before {
        transform: scaleY(1);
    }

    .rule-category-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .rule-category-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-blue), var(--red-accent));
        color: white;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .rule-category-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-900);
        margin: 0;
    }

    .rule-items-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rule-items-list li {
        padding: 16px 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 12px;
        margin-bottom: 12px;
        border-left: 4px solid var(--primary-blue);
        font-size: 15px;
        line-height: 1.7;
        color: var(--text-700);
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .rule-items-list li::before {
        content: '✓';
        color: var(--primary-blue);
        font-weight: 900;
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .rule-items-list li:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .violations-section {
        margin: 50px 0;
    }

    .violation-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .violation-card.warning {
        border-left: 5px solid #ffc107;
    }

    .violation-card.danger {
        border-left: 5px solid #dc3545;
    }

    .violation-card.critical {
        border-left: 5px solid #8b0000;
    }

    .violation-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .violation-level {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .violation-level.warning {
        color: #ffc107;
    }

    .violation-level.danger {
        color: #dc3545;
    }

    .violation-level.critical {
        color: #8b0000;
    }

    .violation-items {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .violation-items li {
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 8px;
        font-size: 14px;
        color: var(--text-700);
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .violation-items li::before {
        content: '⚠';
        font-size: 16px;
        flex-shrink: 0;
    }

    .rights-section,
    .responsibilities-section {
        margin: 50px 0;
    }

    .rights-list,
    .responsibilities-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
    }

    .rights-list li,
    .responsibilities-list li {
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

    .rights-list li::before {
        content: '✓';
        color: #28a745;
        font-weight: 900;
        font-size: 20px;
    }

    .responsibilities-list li::before {
        content: '📋';
        font-size: 18px;
    }

    .rights-list li:hover,
    .responsibilities-list li:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .info-box {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1) 0%, rgba(220, 20, 60, 0.1) 100%);
        border-radius: 20px;
        padding: 32px;
        border: 2px solid var(--primary-blue);
        margin: 40px 0;
    }

    .info-box h4 {
        color: var(--primary-blue);
        font-weight: 800;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-box h4 i {
        font-size: 24px;
    }

    .info-box p {
        color: var(--text-700);
        line-height: 1.8;
        margin: 0;
        font-size: 15px;
    }

    @media (max-width: 768px) {
        .rules-hero {
            padding: 50px 0;
        }

        .rules-hero h1 {
            font-size: 32px;
        }

        .rules-hero p {
            font-size: 16px;
        }

        .rules-main-image {
            height: 250px;
        }

        .rule-category-card {
            padding: 24px;
        }

        .rule-category-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .rule-category-title {
            font-size: 20px;
        }

        .rights-list,
        .responsibilities-list {
            grid-template-columns: 1fr;
        }

        .violation-card {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
<div class="content-section rules-page">
    <div class="container">
        <!-- Hero Section -->
        <div class="rules-hero">
            <h1>NỘI QUY KÝ TÚC XÁ</h1>
            <p>Quy định và hướng dẫn để tạo môi trường sống văn minh, an toàn cho tất cả sinh viên</p>
        </div>

        <!-- Main Image -->
        <div class="text-center mb-5">
            <img src="{{ asset('images/excel-2-2-e1713237797743.jpg') }}" 
                 alt="Nội quy ký túc xá" 
                 class="rules-main-image">
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <h4>
                <i class="fas fa-info-circle"></i>
                Lưu ý quan trọng
            </h4>
            <p>
                Tất cả sinh viên khi vào ở tại ký túc xá VaMos đều phải tuân thủ nghiêm chỉnh các quy định dưới đây. 
                Vi phạm nội quy sẽ bị xử lý theo mức độ từ cảnh cáo đến đình chỉ ở và buộc ra khỏi ký túc xá. 
                Mọi thắc mắc về nội quy, vui lòng liên hệ Ban quản lý ký túc xá.
            </p>
        </div>

        <!-- General Rules Section -->
        <section class="rules-section">
            <h2 class="section-title mb-4">Quy định chung</h2>
            @foreach ($generalRules as $category)
            <div class="rule-category-card">
                <div class="rule-category-header">
                    <div class="rule-category-icon">
                        <i class="fas {{ $category['icon'] }}"></i>
                    </div>
                    <h3 class="rule-category-title">{{ $category['title'] }}</h3>
                </div>
                <ul class="rule-items-list">
                    @foreach ($category['items'] as $item)
                    <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </section>

        <!-- Violations Section -->
        <section class="violations-section panel">
            <h3 class="section-title mb-4">Mức độ xử lý vi phạm</h3>
            <div class="row">
                @foreach ($violations as $violation)
                <div class="col-md-4 mb-4">
                    <div class="violation-card {{ $violation['color'] }}">
                        <div class="violation-level {{ $violation['color'] }}">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $violation['level'] }}
                        </div>
                        <ul class="violation-items">
                            @foreach ($violation['items'] as $item)
                            <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Rights Section -->
        <section class="rights-section panel">
            <h3 class="section-title mb-4">Quyền lợi của sinh viên</h3>
            <ul class="rights-list">
                @foreach ($rights as $right)
                <li>{{ $right }}</li>
                @endforeach
            </ul>
        </section>

        <!-- Responsibilities Section -->
        <section class="responsibilities-section panel">
            <h3 class="section-title mb-4">Trách nhiệm của sinh viên</h3>
            <ul class="responsibilities-list">
                @foreach ($responsibilities as $responsibility)
                <li>{{ $responsibility }}</li>
                @endforeach
            </ul>
        </section>

    </div>
</div>
@endsection

