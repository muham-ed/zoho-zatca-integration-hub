<?php
require_once __DIR__ . '/src/ZatcaHelper.php';
require_once __DIR__ . '/src/ZohoClient.php';
require_once __DIR__ . '/src/OnboardingManager.php';
$config = require __DIR__ . '/config.php';

use App\ZatcaHelper;
use App\ZohoClient;
use App\OnboardingManager;

$zohoClient = new ZohoClient($config);
$onboardingManager = new OnboardingManager();
$clients = $onboardingManager->getClients();

$vatResult = null;
$qrResult = null;

// معالجة فحص الرقم الضريبي
if (isset($_POST['action']) && $_POST['action'] === 'validate_vat') {
    $country = $_POST['country'] ?? 'SA';
    $vat = $_POST['vat_number'] ?? '';
    if ($country === 'SA') {
        $vatResult = ZatcaHelper::validateSaudiVat($vat);
    } else {
        $vatResult = ZatcaHelper::validateUaeTrn($vat);
    }
}

// معالجة توليد كود ZATCA QR
if (isset($_POST['action']) && $_POST['action'] === 'generate_qr') {
    $seller = $_POST['seller_name'] ?? 'شركة العواضي للحلول السحابية';
    $vat = $_POST['seller_vat'] ?? '310123456700003';
    $date = date('c');
    $amount = (float)($_POST['invoice_amount'] ?? 1150.00);
    $tax = (float)($_POST['tax_amount'] ?? 150.00);

    $qrBase64 = ZatcaHelper::generateQrBase64($seller, $vat, $date, $amount, $tax);
    $qrResult = [
        'base64' => $qrBase64,
        'seller' => $seller,
        'vat' => $vat,
        'date' => $date,
        'amount' => $amount,
        'tax' => $tax
    ];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منظومة الربط والصيانة - Zoho Books & CRM & ZATCA</title>
    <!-- Bootstrap RTL & Cairo Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .hero-banner { background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: white; border-radius: 1rem; padding: 2.5rem; margin-bottom: 2rem; }
        .card-custom { border: none; border-radius: 0.85rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07); transition: transform 0.2s; }
        .card-custom:hover { transform: translateY(-2px); }
        .badge-status { font-size: 0.85rem; padding: 0.4rem 0.8rem; border-radius: 2rem; }
    </style>
</head>
<body class="py-4">
<div class="container">

    <!-- Header / Hero Section -->
    <div class="hero-banner shadow">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-primary mb-2 px-3 py-2">Alawadi Cloud Solutions | لوحة القيادة المتكاملة</span>
                <h2 class="fw-bold mb-1">تنفيذ وصيانة أنظمة Zoho Books & Zoho CRM والربط الضريبي</h2>
                <p class="mb-0 text-white-50">إدارة ومتابعة الربط الضريبي (ZATCA / UAE FTA) وتكامل العمليات السحابية في السعودية ودبي</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="onboarding_dashboard.php" class="btn btn-warning fw-bold px-3 py-2">
                    <i class="bi bi-kanban-fill me-1"></i> لوحة تدشين العملاء
                </a>
                <a href="zatca_simulator.php" class="btn btn-success fw-bold px-3 py-2">
                    <i class="bi bi-filetype-xml me-1"></i> محاكي ZATCA UBL 2.1
                </a>
                <a href="sla_maintenance_portal.php" class="btn btn-danger fw-bold px-3 py-2">
                    <i class="bi bi-headset me-1"></i> بوابة طوارئ SLA
                </a>
                <a href="oauth_callback.php" class="btn btn-outline-light fw-bold px-3 py-2">
                    <i class="bi bi-key-fill me-1"></i> Zoho OAuth
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small">حالة ربط Zoho Books</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h5 class="fw-bold mb-0 text-dark">جاهز للتشغيل</h5>
                    <span class="badge bg-success-subtle text-success badge-status"><i class="bi bi-check-circle"></i> جاهز</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small">هيئة الزكاة والضريبة (ZATCA)</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h5 class="fw-bold mb-0 text-dark">المرحلة الثانية</h5>
                    <span class="badge bg-primary-subtle text-primary badge-status"><i class="bi bi-shield-check"></i> UBL 2.1</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small">تكامل Zoho CRM</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h5 class="fw-bold mb-0 text-dark">Closed Won Auto</h5>
                    <span class="badge bg-info-subtle text-info badge-status"><i class="bi bi-arrow-repeat"></i> Deluge Active</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-3 bg-white">
                <div class="text-muted small">عملاء قيد التدشين النشط</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h5 class="fw-bold mb-0 text-dark"><?= count($clients) ?> منشآت نشطة</h5>
                    <span class="badge bg-warning-subtle text-warning badge-status"><i class="bi bi-clock-history"></i> متابعة SLA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Integrated Navigation Shortcuts -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">مسار تدشين العملاء (Onboarding)</h6>
                        <p class="text-muted small mb-0">متابعة إنجاز الـ 17 مهمة، وقوالب CSV للأرصدة والشجرة</p>
                    </div>
                    <a href="onboarding_dashboard.php" class="btn btn-sm btn-outline-warning fw-bold">فتح المسار</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">محاكي فواتير ZATCA الحية</h6>
                        <p class="text-muted small mb-0">توليد ملفات XML وحساب الهاش والاعتماد B2B & B2C</p>
                    </div>
                    <a href="zatca_simulator.php" class="btn btn-sm btn-outline-success fw-bold">فتح المحاكي</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">بوابة طوارئ الصيانة (SLA Desk)</h6>
                        <p class="text-muted small mb-0">تذاكر الصيانة الدورية والاستجابة خلال 30 دقيقة</p>
                    </div>
                    <a href="sla_maintenance_portal.php" class="btn btn-sm btn-outline-danger fw-bold">فتح البوابة</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tools Grid -->
    <div class="row g-4 mb-4">

        <!-- Tool 1: مولد ومحاكي الـ QR Code الخاص بهيئة الزكاة -->
        <div class="col-lg-6">
            <div class="card card-custom h-100 bg-white p-4">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="bi bi-qr-code-scan me-2"></i> مولد ومحاكي رمز QR المعتمد من ZATCA (TLV Base64)
                </h5>
                <p class="text-muted small">توليد شفرة الفاتورة الإلكترونية المعتمدة بنظام TLV المشفر طبقاً لمتطلبات الفوترة في المملكة العربية السعودية.</p>

                <form method="POST">
                    <input type="hidden" name="action" value="generate_qr">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم المنشأة / المورد:</label>
                        <input type="text" name="seller_name" class="form-control" value="شركة العواضي للحلول السحابية" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الرقم الضريبي للمنشأة (15 رقماً):</label>
                        <input type="text" name="seller_vat" class="form-control" value="310123456700003" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">إجمالي الفاتورة مع الضريبة (SAR):</label>
                            <input type="number" step="0.01" name="invoice_amount" class="form-control" value="1150.00" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">قيمة ضريبة 15% (SAR):</label>
                            <input type="number" step="0.01" name="tax_amount" class="form-control" value="150.00" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-cpu me-1"></i> توليد شفرة الـ QR (TLV Base64)
                    </button>
                </form>

                <?php if ($qrResult): ?>
                    <div class="alert alert-success mt-3 mb-0">
                        <div class="fw-bold mb-1">تم توليد الشفرة بنجاح:</div>
                        <textarea class="form-control font-monospace small mb-3" rows="3" readonly><?= htmlspecialchars($qrResult['base64']) ?></textarea>
                        <div class="text-center">
                            <!-- عرض QR Code مباشرة باستخدام خدمة سريعة -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=<?= urlencode($qrResult['base64']) ?>" alt="ZATCA QR" class="img-thumbnail shadow-sm">
                            <div class="text-muted small mt-2">يمكنك مسح هذا الرمز بأي تطبيق معتمد للفوترة الإلكترونية (ZATCA)</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tool 2: مدقق الأرقام الضريبية الذكي -->
        <div class="col-lg-6">
            <div class="card card-custom h-100 bg-white p-4">
                <h5 class="fw-bold text-success mb-3">
                    <i class="bi bi-patch-check-fill me-2"></i> فاحص الأرقام الضريبية (KSA VAT & UAE TRN Validator)
                </h5>
                <p class="text-muted small">فحص وتدقيق الرقم الضريبي للعملاء والموردين قبل ترحيلهم إلى Zoho Books لضمان قبول الفواتير لدى الهيئات الضريبية.</p>

                <form method="POST">
                    <input type="hidden" name="action" value="validate_vat">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الدولة المستهدفة:</label>
                        <select name="country" class="form-select">
                            <option value="SA">المملكة العربية السعودية (ZATCA - 15 رقماً يبدأ بـ 3)</option>
                            <option value="AE">دولة الإمارات العربية المتحدة (FTA TRN - 15 رقماً)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الرقم الضريبي المراد فحصه:</label>
                        <input type="text" name="vat_number" class="form-control" placeholder="مثال: 310123456700003" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 fw-bold">
                        <i class="bi bi-search me-1"></i> فحص ومطابقة الرقم
                    </button>
                </form>

                <?php if ($vatResult): ?>
                    <div class="alert <?= $vatResult['valid'] ? 'alert-success' : 'alert-danger' ?> mt-3 mb-0">
                        <div class="fw-bold">
                            <i class="bi <?= $vatResult['valid'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?> me-1"></i>
                            <?= $vatResult['valid'] ? 'الرقم متوافق ومعتمد' : 'الرقم غير صالح' ?>
                        </div>
                        <div class="small mt-1"><?= htmlspecialchars($vatResult['message']) ?></div>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <div class="bg-light p-3 rounded">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-journal-code me-1"></i> الأدلة الفنية والملفات التشغيلية المعتمدة:</h6>
                    <ul class="list-unstyled mb-0 small text-secondary">
                        <li class="mb-1">📘 <a href="USER_TRAINING_MANUAL.md" target="_blank" class="text-decoration-none fw-semibold">دليل تدريب وتشغيل المستخدم والمحاسبين (USER_TRAINING_MANUAL.md)</a></li>
                        <li class="mb-1">📄 <a href="PROPOSAL_MOSTAQL.md" target="_blank" class="text-decoration-none">عرض التقديم الرسمي لمشروع مستقل (PROPOSAL_MOSTAQL.md)</a></li>
                        <li class="mb-1">⚙️ <a href="ZATCA_ZOHO_INTEGRATION_GUIDE.md" target="_blank" class="text-decoration-none">دليل الربط التقني مع هيئة الزكاة (ZATCA_ZOHO_INTEGRATION_GUIDE.md)</a></li>
                        <li class="mb-1">💻 <a href="ZOHO_CRM_BOOKS_DELUGE_SCRIPTS.md" target="_blank" class="text-decoration-none">سكربتات Deluge لربط الصفقات وأتمتة الفواتير (ZOHO_CRM_BOOKS_DELUGE_SCRIPTS.md)</a></li>
                        <li>📋 <a href="CLIENT_ONBOARDING_CHECKLIST.md" target="_blank" class="text-decoration-none">قائمة فحص تدشين العملاء في السعودية والإمارات (CLIENT_ONBOARDING_CHECKLIST.md)</a></li>
                    </ul>
                </div>

            </div>
        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
