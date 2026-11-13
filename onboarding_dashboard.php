<?php
/**
 * ============================================================================
 * لوحة إدارة ومتابعة تدشين عملاء Zoho Books & CRM والربط الضريبي
 * Client Onboarding & Project Delivery Management Dashboard
 * ============================================================================
 *
 * المطور: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 */

require_once __DIR__ . '/src/OnboardingManager.php';
require_once __DIR__ . '/src/DataTemplates.php';

use App\OnboardingManager;
use App\DataTemplates;

$manager = new OnboardingManager();
$message = null;

// معالجة إضافة عميل جديد
if (isset($_POST['action']) && $_POST['action'] === 'add_client') {
    $newClient = $manager->addClient([
        'name' => $_POST['contact_name'] ?? '',
        'company' => $_POST['company_name'] ?? '',
        'country' => $_POST['country'] ?? 'SA',
        'vat_number' => $_POST['vat_number'] ?? '',
        'cr_number' => $_POST['cr_number'] ?? '',
        'contact_email' => $_POST['contact_email'] ?? '',
        'contact_phone' => $_POST['contact_phone'] ?? ''
    ]);
    $message = "تمت إضافة العميل المنشأة ({$newClient['company']}) بنجاح وبدء مسار التدشين!";
}

// معالجة تحديث مهمة معينة
if (isset($_POST['action']) && $_POST['action'] === 'toggle_task') {
    $clientId = $_POST['client_id'] ?? '';
    $taskKey = $_POST['task_key'] ?? '';
    $currentStatus = (bool)($_POST['current_status'] ?? false);
    $manager->updateTaskStatus($clientId, $taskKey, !$currentStatus);
    $message = "تم تحديث حالة المهمة بنجاح!";
}

// تنزيل نماذج CSV
if (isset($_GET['download'])) {
    $type = $_GET['download'];
    if ($type === 'customers') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Zoho_Books_Customers_Import_Template.csv"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        echo DataTemplates::getCustomerImportCsvTemplate();
        exit;
    } elseif ($type === 'items') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Zoho_Books_Items_Import_Template.csv"');
        echo "\xEF\xBB\xBF";
        echo DataTemplates::getItemsImportCsvTemplate();
        exit;
    } elseif ($type === 'opening_balances') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Zoho_Books_Opening_Balances_Journal.csv"');
        echo "\xEF\xBB\xBF";
        echo DataTemplates::getOpeningBalancesJournalTemplate();
        exit;
    }
}

$clients = $manager->getClients();

$taskLabels = [
    'doc_cr_vat' => 'استلام السجل التجاري وشهادة ضريبة القيمة المضافة',
    'doc_national_address' => 'العنوان الوطني المفصل (المبنى، الشارع، الرمز البريدي)',
    'chart_of_accounts' => 'تهيئة شجرة الحسابات طبقاً لمعايير المحاسبة المعتمدة',
    'opening_balances' => 'إدخال ومطابقة الأرصدة الافتتاحية وميزان المراجعة',
    'import_contacts_items' => 'استيراد دليل العملاء والموردين وقوائم الأصناف والخدمات',

    'zatca_otp_obtained' => 'استخراج رمز المرور لمرة واحدة (OTP) من بوابة فاتورة',
    'zatca_egs_registered' => 'تسجيل وحدة توليد الفواتير (EGS Unit) بالهيئة',
    'zatca_csid_compliance' => 'استخراج شهادة الامتثال والإنتاج (CSID Certificates)',
    'zatca_invoice_template' => 'تثبيت قوالب الفواتير المعتمدة (عربي/إنجليزي مع TLV QR)',
    'zatca_b2b_clearance_test' => 'اختبار اعتماد فاتورة قياسية B2B (Clearance API)',
    'zatca_b2c_reporting_test' => 'اختبار إبلاغ فاتورة مبسطة B2C (Reporting API)',

    'crm_books_connected' => 'تفعيل الربط المباشر بين Zoho CRM و Zoho Books',
    'crm_field_mapping' => 'مطابقة الحقول المشتركة بين جهات الاتصال والصفقات',
    'crm_deluge_scripts_installed' => 'تثبيت سكريبتات Deluge لأتمتة تحويل الصفقات إلى فواتير',
    'user_roles_permissions' => 'تحديد أدوار وصلاحيات المستخدمين (محاسبة / مبيعات)',

    'training_session_conducted' => 'عقد ورشة العمل التدريبية لفريق العمل',
    'user_manual_delivered' => 'تسليم العميل الدليل التشغيلي المبسط للفواتير',
    'sla_channel_activated' => 'تفعيل قناة الدعم الفني السريع وحالات الطوارئ (SLA)'
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة تدشين ومتابعة العملاء - Zoho & ZATCA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .hero-banner { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; }
        .card-custom { border: none; border-radius: 0.85rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07); }
        .task-done { text-decoration: line-through; color: #94a3b8; }
    </style>
</head>
<body class="py-4">
<div class="container">

    <!-- Header -->
    <div class="hero-banner shadow">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-indigo-500 mb-2 px-3 py-1 fw-bold bg-primary">Client Onboarding Portal</span>
                <h3 class="fw-bold mb-1">لوحة تدشين ومتابعة عملاء Zoho Books & CRM والربط الضريبي</h3>
                <p class="mb-0 text-white-50">متابعة تنفيذ مراحل التهيئة المحاسبية، شهادات ZATCA، وتكامل Deluge لعملاء السعودية والإمارات</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-warning fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#addClientModal">
                    <i class="bi bi-person-plus-fill me-1"></i> إضافة عميل جديد
                </button>
                <a href="index.php" class="btn btn-outline-light px-3 py-2">
                    <i class="bi bi-arrow-right-circle me-1"></i> لوحة التحكم
                </a>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Downloadable CSV Templates Banner -->
    <div class="card card-custom p-3 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-success me-1"></i> نماذج استيراد البيانات الجاهزة لـ Zoho Books (CSV):
                </h6>
                <div class="text-muted small">نماذج جاهزة للتعبئة والاستيراد الفوري في شجرة الحسابات والعملاء والأصناف</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="?download=customers" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i> دليل العملاء (Customers)
                </a>
                <a href="?download=items" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download me-1"></i> الأصناف والخدمات (Items)
                </a>
                <a href="?download=opening_balances" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> الأرصدة الافتتاحية (Journal)
                </a>
            </div>
        </div>
    </div>

    <!-- Active Clients Execution Cards -->
    <div class="row g-4">
        <?php foreach ($clients as $client): ?>
            <?php $progress = OnboardingManager::calculateProgress($client); ?>
            <div class="col-12">
                <div class="card card-custom bg-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-3 mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($client['company']) ?></h5>
                                <span class="badge <?= $client['country'] === 'SA' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $client['country'] === 'SA' ? 'المملكة العربية السعودية (15%)' : 'دولة الإمارات (5%)' ?>
                                </span>
                                <span class="badge bg-secondary">كود: <?= htmlspecialchars($client['id']) ?></span>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-person me-1"></i> <?= htmlspecialchars($client['name']) ?> |
                                <i class="bi bi-hash me-1"></i> الضريبي: <?= htmlspecialchars($client['vat_number'] ?: 'غير مسجل') ?> |
                                <i class="bi bi-building me-1"></i> السجل: <?= htmlspecialchars($client['cr_number'] ?: 'غير مسجل') ?> |
                                <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($client['contact_email']) ?>
                            </div>
                        </div>

                        <div class="text-end" style="min-width: 200px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small">نسبة الإنجاز الكلي:</span>
                                <span class="fw-bold text-primary"><?= $progress ?>%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar <?= $progress == 100 ? 'bg-success' : 'bg-primary' ?>" style="width: <?= $progress ?>%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Implementation Phases Accordion -->
                    <div class="row g-3">
                        <!-- Phase 1 -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-1-circle-fill me-1"></i> التهيئة المحاسبية
                                </h6>
                                <?php foreach (['doc_cr_vat', 'doc_national_address', 'chart_of_accounts', 'opening_balances', 'import_contacts_items'] as $taskKey): ?>
                                    <form method="POST" class="mb-2">
                                        <input type="hidden" name="action" value="toggle_task">
                                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['id']) ?>">
                                        <input type="hidden" name="task_key" value="<?= $taskKey ?>">
                                        <input type="hidden" name="current_status" value="<?= $client['tasks'][$taskKey] ? 1 : 0 ?>">
                                        <button type="submit" class="btn btn-sm text-start w-100 p-0 border-0 bg-transparent">
                                            <i class="bi <?= $client['tasks'][$taskKey] ? 'bi-check-square-fill text-success' : 'bi-square text-secondary' ?> me-1"></i>
                                            <span class="small <?= $client['tasks'][$taskKey] ? 'task-done' : 'fw-semibold text-dark' ?>">
                                                <?= htmlspecialchars($taskLabels[$taskKey]) ?>
                                            </span>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Phase 2 -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="bi bi-2-circle-fill me-1"></i> الربط الضريبي (ZATCA)
                                </h6>
                                <?php foreach (['zatca_otp_obtained', 'zatca_egs_registered', 'zatca_csid_compliance', 'zatca_invoice_template', 'zatca_b2b_clearance_test', 'zatca_b2c_reporting_test'] as $taskKey): ?>
                                    <form method="POST" class="mb-2">
                                        <input type="hidden" name="action" value="toggle_task">
                                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['id']) ?>">
                                        <input type="hidden" name="task_key" value="<?= $taskKey ?>">
                                        <input type="hidden" name="current_status" value="<?= $client['tasks'][$taskKey] ? 1 : 0 ?>">
                                        <button type="submit" class="btn btn-sm text-start w-100 p-0 border-0 bg-transparent">
                                            <i class="bi <?= $client['tasks'][$taskKey] ? 'bi-check-square-fill text-success' : 'bi-square text-secondary' ?> me-1"></i>
                                            <span class="small <?= $client['tasks'][$taskKey] ? 'task-done' : 'fw-semibold text-dark' ?>">
                                                <?= htmlspecialchars($taskLabels[$taskKey]) ?>
                                            </span>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Phase 3 -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="fw-bold text-info mb-3">
                                    <i class="bi bi-3-circle-fill me-1"></i> تكامل CRM & Deluge
                                </h6>
                                <?php foreach (['crm_books_connected', 'crm_field_mapping', 'crm_deluge_scripts_installed', 'user_roles_permissions'] as $taskKey): ?>
                                    <form method="POST" class="mb-2">
                                        <input type="hidden" name="action" value="toggle_task">
                                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['id']) ?>">
                                        <input type="hidden" name="task_key" value="<?= $taskKey ?>">
                                        <input type="hidden" name="current_status" value="<?= $client['tasks'][$taskKey] ? 1 : 0 ?>">
                                        <button type="submit" class="btn btn-sm text-start w-100 p-0 border-0 bg-transparent">
                                            <i class="bi <?= $client['tasks'][$taskKey] ? 'bi-check-square-fill text-success' : 'bi-square text-secondary' ?> me-1"></i>
                                            <span class="small <?= $client['tasks'][$taskKey] ? 'task-done' : 'fw-semibold text-dark' ?>">
                                                <?= htmlspecialchars($taskLabels[$taskKey]) ?>
                                            </span>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Phase 4 -->
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded p-3 h-100 bg-light">
                                <h6 class="fw-bold text-warning mb-3">
                                    <i class="bi bi-4-circle-fill me-1"></i> التدريب والتسليم (SLA)
                                </h6>
                                <?php foreach (['training_session_conducted', 'user_manual_delivered', 'sla_channel_activated'] as $taskKey): ?>
                                    <form method="POST" class="mb-2">
                                        <input type="hidden" name="action" value="toggle_task">
                                        <input type="hidden" name="client_id" value="<?= htmlspecialchars($client['id']) ?>">
                                        <input type="hidden" name="task_key" value="<?= $taskKey ?>">
                                        <input type="hidden" name="current_status" value="<?= $client['tasks'][$taskKey] ? 1 : 0 ?>">
                                        <button type="submit" class="btn btn-sm text-start w-100 p-0 border-0 bg-transparent">
                                            <i class="bi <?= $client['tasks'][$taskKey] ? 'bi-check-square-fill text-success' : 'bi-square text-secondary' ?> me-1"></i>
                                            <span class="small <?= $client['tasks'][$taskKey] ? 'task-done' : 'fw-semibold text-dark' ?>">
                                                <?= htmlspecialchars($taskLabels[$taskKey]) ?>
                                            </span>
                                        </button>
                                    </form>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Modal: Add New Client -->
<div class="modal fade" id="addClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">إضافة منشأة / عميل جديد لمسار التدشين</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_client">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم المنشأة / الشركة:</label>
                        <input type="text" name="company_name" class="form-control" placeholder="مثال: شركة الرواد للتجارة" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم المسؤول / جهة الاتصال:</label>
                        <input type="text" name="contact_name" class="form-control" placeholder="مثال: م. فهد الشمري" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">الدولة:</label>
                            <select name="country" class="form-select">
                                <option value="SA">المملكة العربية السعودية</option>
                                <option value="AE">دولة الإمارات العربية المتحدة</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">رقم السجل التجاري:</label>
                            <input type="text" name="cr_number" class="form-control" placeholder="1010XXXXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">الرقم الضريبي:</label>
                        <input type="text" name="vat_number" class="form-control" placeholder="310XXXXXXXX0003">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">البريد الإلكتروني:</label>
                            <input type="email" name="contact_email" class="form-control" placeholder="info@company.com">
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">رقم الجوال:</label>
                            <input type="text" name="contact_phone" class="form-control" placeholder="+9665XXXXXXX">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold">حفظ وبدء التدشين</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
