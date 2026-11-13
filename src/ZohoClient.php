<?php
namespace App;

/**
 * فئة التعامل مع Zoho Books & Zoho CRM APIs (OAuth 2.0)
 */
class ZohoClient
{
    private array $config;
    private string $tokenFile;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->tokenFile = __DIR__ . '/../data/access_token.json';
        if (!is_dir(__DIR__ . '/../data')) {
            mkdir(__DIR__ . '/../data', 0777, true);
        }
    }

    /**
     * الحصول على Access Token صالح (يتم التجديد تلقائياً عبر Refresh Token)
     */
    public function getValidAccessToken(): ?string
    {
        if (file_exists($this->tokenFile)) {
            $data = json_decode(file_get_contents($this->tokenFile), true);
            if ($data && isset($data['access_token']) && time() < ($data['expires_at'] - 60)) {
                return $data['access_token'];
            }
        }

        return $this->refreshAccessToken();
    }

    /**
     * تجديد Access Token
     */
    public function refreshAccessToken(): ?string
    {
        $zohoConfig = $this->config['zoho'];
        if (empty($zohoConfig['refresh_token']) || empty($zohoConfig['client_id'])) {
            return null;
        }

        $url = rtrim($zohoConfig['accounts_server'], '/') . '/oauth/v2/token';
        $params = [
            'refresh_token' => $zohoConfig['refresh_token'],
            'client_id' => $zohoConfig['client_id'],
            'client_secret' => $zohoConfig['client_secret'],
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($result && isset($result['access_token'])) {
            $dataToSave = [
                'access_token' => $result['access_token'],
                'expires_at' => time() + ($result['expires_in'] ?? 3600),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            file_put_contents($this->tokenFile, json_encode($dataToSave, JSON_PRETTY_PRINT));
            return $result['access_token'];
        }

        return null;
    }

    /**
     * تنفيذ طلب إلى Zoho Books API
     */
    public function callBooks(string $endpoint, string $method = 'GET', array $params = [], array $data = []): array
    {
        $token = $this->getValidAccessToken();
        if (!$token) {
            return ['code' => -1, 'message' => 'لم يتم إعداد أو ربط رمز المصادقة (Access Token) بعد.'];
        }

        $orgId = $this->config['zoho']['organization_id'];
        $url = rtrim($this->config['zoho']['books_api_domain'], '/') . '/' . ltrim($endpoint, '/');

        $params['organization_id'] = $orgId;
        $url .= '?' . http_build_query($params);

        $headers = [
            'Authorization: Zoho-oauthtoken ' . $token,
            'Content-Type: application/json;charset=UTF-8'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: ['code' => -2, 'message' => 'استجابة غير صالحة من السيرفر', 'raw' => $response];
    }

    /**
     * تنفيذ طلب إلى Zoho CRM API
     */
    public function callCrm(string $module, string $method = 'GET', array $params = [], array $data = []): array
    {
        $token = $this->getValidAccessToken();
        if (!$token) {
            return ['code' => -1, 'message' => 'لم يتم إعداد أو ربط رمز المصادقة (Access Token) بعد.'];
        }

        $url = rtrim($this->config['zoho']['crm_api_domain'], '/') . '/' . ltrim($module, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Authorization: Zoho-oauthtoken ' . $token,
            'Content-Type: application/json;charset=UTF-8'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?: ['code' => -2, 'message' => 'استجابة غير صالحة من السيرفر', 'raw' => $response];
    }
}
