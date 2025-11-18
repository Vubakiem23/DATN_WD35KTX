<style>
    .notification-form-wrapper {
        max-width: 1100px;
        margin: 0 auto 56px;
    }

    .nf-page-header {
        background: linear-gradient(135deg, #1d4ed8, #312e81);
        color: #fff;
        padding: 36px 40px;
        border-radius: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }

    .nf-page-header::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.25), transparent 60%);
        opacity: 0.8;
    }

    .nf-page-header > * {
        position: relative;
        z-index: 1;
    }

    .nf-eyebrow {
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, 0.8);
    }

    .nf-title {
        font-weight: 800;
        margin-bottom: 10px;
    }

    .nf-subtitle {
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.88);
        max-width: 520px;
        line-height: 1.5;
    }

    .nf-header-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .nf-chip {
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .nf-chip--primary {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
    }

    .nf-chip--subtle {
        background: rgba(79, 70, 229, 0.12);
        color: #4f46e5;
    }

    .nf-meta-text {
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
    }

    .notification-form-card {
        background: #fff;
        margin-top: -32px;
        padding: 36px;
        border-radius: 28px;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.1);
        border: 1px solid rgba(15, 23, 42, 0.06);
    }

    .nf-section + .nf-section {
        margin-top: 32px;
        padding-top: 32px;
        border-top: 1px dashed rgba(148, 163, 184, 0.6);
    }

    .nf-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }

    .nf-section-eyebrow {
        font-size: 12px;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #6366f1;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .nf-section-title {
        font-weight: 700;
        margin-bottom: 0;
        color: #0f172a;
    }

    .notification-form-card .form-label {
        font-weight: 600;
        color: #0f172a;
    }

    .notification-form-card .form-control,
    .notification-form-card .form-select {
        border-radius: 14px;
        border: 1px solid #d1d5db;
        padding: 10px 14px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .notification-form-card .form-control:focus,
    .notification-form-card .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .nf-inline-controls {
        display: flex;
        align-items: center;
    }

    .nf-inline-controls .btn {
        border-radius: 12px;
        font-weight: 600;
    }

    .nf-hint-text {
        color: #64748b;
        font-size: 13px;
    }

    .nf-attachment-box {
        border: 1px dashed rgba(99, 102, 241, 0.4);
        border-radius: 16px;
        padding: 18px;
        background: rgba(248, 250, 255, 0.8);
    }

    .nf-preview-thumb img {
        width: 100%;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.2);
    }

    .nf-link {
        color: #2563eb;
        font-weight: 600;
        text-decoration: none;
    }

    .nf-link:hover {
        text-decoration: underline;
    }

    .nf-form-actions {
        margin-top: 32px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    .nf-form-actions .btn {
        border-radius: 999px;
        padding-inline: 28px;
        font-weight: 600;
        min-height: 46px;
    }

    @media (max-width: 767px) {
        .nf-page-header {
            padding: 28px;
        }

        .notification-form-card {
            padding: 28px;
        }
    }

    @media (max-width: 575px) {
        .notification-form-card {
            padding: 22px;
        }

        .nf-inline-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .nf-inline-controls .btn {
            width: 100%;
        }
    }
</style>

