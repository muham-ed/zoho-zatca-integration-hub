<?php
/**
 * استقبال كود المصادقة وتوليد الـ Refresh Token و Access Token تلقائياً
 */

$config = require __DIR__ . '/config.php';

$code = $_GET['code'] ?? null;
$error = $_GET['error'] ?? null;

$message = '';
$tokenData = null;

if ($code) {
    $url = rtrim($config['zoho']['accounts_server'], '/') . '/oauth/v2/token';
    $params = [
        'code' => $code,
        'client_id' => $config['zoho']['client_id'],
        'client_secret' => $config['zoho']['client_secret'],
        'redirect_uri' => $config['zoho']['redirect_uri'],
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['refresh_token'])) {
        // حفظ التوكن في ملف data
        if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0777, true);
        }
        $saveData = [
            'access_token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'],
            'expires_at' => time() + ($tokenData['expires_in'] ?? 3600),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        file_put_contents(__DIR__ . '/data/access_token.json', json_encode($saveData, JSON_PRETTY_PRINT));
        $message = "تم استخراج Refresh Token بنجاح وحفظه في النظام!";
    } else {
        $message = "فشل استخراج التوكن: " . ($tokenData['error'] ?? 'خطأ غير معروف');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعداد تفويض Zoho OAuth 2.0</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Cairo', sans-serif; background-color: #f8fafc; }</style>
</head>
<body class="py-5">
<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width: 650px;">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold">إعداد وتفعيل الربط (Zoho OAuth 2.0)</h5>
        </div>
        <div class="card-body p-4">
            <?php if ($message): ?>
                <div class="alert <?= isset($tokenData['refresh_token']) ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
                <?php if (isset($tokenData['refresh_token'])): ?>
                    <p class="fw-bold mb-2">انسخ الـ Refresh Token وضعه في <code>config.php</code>:</p>
                    <textarea class="form-control mb-3" rows="3" readonly><?= htmlspecialchars($tokenData['refresh_token']) ?></textarea>
                    <a href="index.php" class="btn btn-success w-100">العودة إلى لوحة التحكم الرئيسية</a>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted">لبدء عملية الربط مع Zoho Books & CRM، يمكنك الضغط على الزر التالي للمصادقة:</p>
                <?php
                $authUrl = rtrim($config['zoho']['accounts_server'], '/') . '/oauth/v2/auth?' . http_build_query([
                    'scope' => 'ZohoBooks.fullaccess.all,ZohoCRM.modules.ALL,ZohoCRM.settings.ALL',
                    'client_id' => $config['zoho']['client_id'],
                    'response_type' => 'code',
                    'access_type' => 'offline',
                    'redirect_uri' => $config['zoho']['redirect_uri'],
                    'prompt' => 'consent'
                ]);
                ?>
                <div class="d-grid gap-2">
                    <a href="<?= htmlspecialchars($authUrl) ?>" class="btn btn-primary btn-lg">تسجيل الدخول والتفويض عبر Zoho</a>
                    <a href="index.php" class="btn btn-outline-secondary">العودة للوحة التحكم</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
