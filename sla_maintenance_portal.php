<?php
/**
 * ============================================================================
 * بوابة الصيانة والدعم الفني السريع وحالات الطوارئ (SLA Emergency & Maintenance)
 * ============================================================================
 *
 * المطور: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 */

$dataFile = __DIR__ . '/data/sla_tickets.json';
if (!file_exists($dataFile)) {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }
    $initialTickets = [
        [
            'id' => 'TICK-901',
            'client_name' => 'شركة آفاق السحاب للاتصالات',
            'severity' => 'CRITICAL', // CRITICAL (30m SLA), HIGH (2h SLA), NORMAL (24h SLA)
            'category' => 'ZATCA Clearance Failure',
            'title' => 'رفض فاتورة B2B رقم INV-4091 من بوابة فاتورة بسبب خطأ في الـ PIH',
            'status' => 'RESOLVED',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'resolved_at' => date('Y-m-d H:i:s', strtotime('-23 hours')),
            'solution' => 'تم فحص تسلسل الفواتير، وتبين وجود مسودة ملغاة يدوياً، تم تعديل الـ Previous Invoice Hash وإعادة ترحيل الفاتورة بنجاح وحصولها على الختم.'
        ],
        [
            'id' => 'TICK-902',
            'client_name' => 'مؤسسة الواحة الذكية - دبي',
            'severity' => 'HIGH',
            'category' => 'Zoho CRM Sync Issue',
            'title' => 'عدم مزامنة صفقة مغلقة رابحة (Deal Closed Won) إلى Zoho Books تلقائياً',
            'status' => 'IN_PROGRESS',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'resolved_at' => null,
            'solution' => 'جاري مراجعة سجلات تشغيل Deluge Webhook والتأكد من مطابقة اسم حقل العملة ومعرف العميل.'
        ]
    ];
    file_put_contents($dataFile, json_encode($initialTickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$tickets = json_decode(file_get_contents($dataFile), true) ?: [];
$message = null;

if (isset($_POST['action']) && $_POST['action'] === 'create_ticket') {
    $newTicket = [
        'id' => 'TICK-' . rand(100, 999),
        'client_name' => $_POST['client_name'] ?? 'عميل جديد',
        'severity' => $_POST['severity'] ?? 'NORMAL',
        'category' => $_POST['category'] ?? 'General Support',
        'title' => $_POST['title'] ?? '',
        'status' => 'OPEN',
        'created_at' => date('Y-m-d H:i:s'),
        'resolved_at' => null,
        'solution' => ''
    ];
    array_unshift($tickets, $newTicket);
    file_put_contents($dataFile, json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $message = "تم تسجيل بلاغ الصيانة برقم ({$newTicket['id']}) وإخطار مهندس الدعم المناوب فوراً طبقاً لـ SLA!";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الصيانة واتفاقية مستوى الخدمة (SLA Portal) - شركة العواضي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .hero-banner { background: linear-gradient(135deg, #831843 0%, #be123c 100%); color: white; border-radius: 1rem; padding: 2rem; margin-bottom: 2rem; }
        .card-custom { border: none; border-radius: 0.85rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07); }
    </style>
</head>
<body class="py-4">
<div class="container">

    <!-- Header -->
    <div class="hero-banner shadow">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-light text-danger mb-2 px-3 py-1 fw-bold">24/7 SLA Support Desk</span>
                <h3 class="fw-bold mb-1">بوابة الصيانة والدعم الفني السريع وحالات الطوارئ (SLA)</h3>
                <p class="mb-0 text-white-70">استجابة وحلول فورية لمشاكل الفوترة الإلكترونية، أخطاء الربط مع ZATCA، وتوقفات Zoho</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-warning fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                    <i class="bi bi-exclamation-octagon-fill me-1"></i> فتح تذكرة طوارئ جديدة
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

    <!-- SLA Response Time Guarantees -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-danger">
                <div class="fw-bold text-danger"><i class="bi bi-lightning-charge-fill me-1"></i> الطوارئ الحرجة (Critical)</div>
                <div class="h4 fw-bold mt-1 mb-0 text-dark">خلال 30 دقيقة</div>
                <div class="text-muted small mt-1">توقف الفوترة أو رفض فواتير ZATCA بالكامل</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
                <div class="fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> أعطال عالية (High Priority)</div>
                <div class="h4 fw-bold mt-1 mb-0 text-dark">خلال ساعتين (2 Hours)</div>
                <div class="text-muted small mt-1">توقف مزامنة Deluge CRM أو خطأ في الحسابات</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-3 bg-white border-start border-4 border-info">
                <div class="fw-bold text-info"><i class="bi bi-clock-history me-1"></i> الاستفسارات الدورية (Normal)</div>
                <div class="h4 fw-bold mt-1 mb-0 text-dark">خلال 12-24 ساعة</div>
                <div class="text-muted small mt-1">تعديل قوالب الفواتير، استخراج تقارير ضريبية، وتدريب جديد</div>
            </div>
        </div>
    </div>

    <!-- Active Tickets Table -->
    <div class="card card-custom p-4 bg-white shadow-sm mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-shaded me-2"></i> سجل بلاغات وتذاكر الصيانة الدورية:</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم التذكرة</th>
                        <th>العميل / المنشأة</th>
                        <th>مستوى الأهمية (SLA)</th>
                        <th>التصنيف</th>
                        <th>عنوان البلاغ والحل الفني</th>
                        <th>الحالة</th>
                        <th>وقت التسجيل</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-primary"><?= htmlspecialchars($t['id']) ?></td>
                            <td class="fw-semibold"><?= htmlspecialchars($t['client_name']) ?></td>
                            <td>
                                <?php if ($t['severity'] === 'CRITICAL'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-fire"></i> حرجة (30 دقيقة)</span>
                                <?php elseif ($t['severity'] === 'HIGH'): ?>
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> عالية (ساعتان)</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">عادية</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['category']) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($t['title']) ?></div>
                                <?php if (!empty($t['solution'])): ?>
                                    <div class="small text-success mt-1"><i class="bi bi-check2-all me-1"></i> <strong>الإجراء المنفذ:</strong> <?= htmlspecialchars($t['solution']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['status'] === 'RESOLVED'): ?>
                                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle"></i> تم الحل</span>
                                <?php elseif ($t['status'] === 'IN_PROGRESS'): ?>
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1"><i class="bi bi-gear-wide-connected"></i> قيد المتابعة</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="bi bi-envelope-exclamation"></i> مفتوحة جديدة</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted font-monospace"><?= htmlspecialchars($t['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Ticket -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">تسجيل بلاغ صيانة طارئ (SLA Emergency Ticket)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_ticket">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">اسم المنشأة / العميل:</label>
                        <input type="text" name="client_name" class="form-control" placeholder="اسم الشركة" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">مستوى الخطورة (SLA Level):</label>
                            <select name="severity" class="form-select">
                                <option value="CRITICAL">حرج (Critical - توقف تام للفوترة)</option>
                                <option value="HIGH">عالي (High - خطأ في الربط أو الحسابات)</option>
                                <option value="NORMAL" selected>عادي (Normal - استفسار أو تعديل تقرير)</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">التصنيف الفني:</label>
                            <select name="category" class="form-select">
                                <option value="ZATCA Clearance Failure">أخطاء ZATCA والربط الضريبي</option>
                                <option value="Zoho CRM Sync Issue">تكامل ومزامنة Zoho CRM</option>
                                <option value="Chart of Accounts / Tax">شجرة الحسابات والضرائب</option>
                                <option value="General Support">دعم فني وتدريب عام</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">عنوان وتفاصيل البلاغ:</label>
                        <textarea name="title" rows="3" class="form-control" placeholder="اكتب وصفاً مختصراً للمشكلة ورسالة الخطأ إن وجدت..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger fw-bold">إرسال البلاغ فوراً</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
