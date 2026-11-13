<?php
namespace App;

/**
 * ============================================================================
 * مدير تدشين ومتابعة العملاء (Client Onboarding & Project Execution Engine)
 * ============================================================================
 *
 * المطور: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 */
class OnboardingManager
{
    private string $storageFile;

    public function __construct()
    {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->storageFile = $dir . '/onboarding_clients.json';
        if (!file_exists($this->storageFile)) {
            $this->seedInitialData();
        }
    }

    /**
     * الحصول على جميع العملاء
     */
    public function getClients(): array
    {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $data = json_decode(file_get_contents($this->storageFile), true);
        return is_array($data) ? $data : [];
    }

    /**
     * حفظ قائمة العملاء
     */
    public function saveClients(array $clients): bool
    {
        return (bool)file_put_contents($this->storageFile, json_encode($clients, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * إضافة عميل جديد
     */
    public function addClient(array $clientData): array
    {
        $clients = $this->getClients();
        $id = 'CLI-' . strtoupper(substr(uniqid(), -6));

        $newClient = [
            'id' => $id,
            'name' => $clientData['name'] ?? '',
            'company' => $clientData['company'] ?? '',
            'country' => $clientData['country'] ?? 'SA',
            'vat_number' => $clientData['vat_number'] ?? '',
            'cr_number' => $clientData['cr_number'] ?? '',
            'contact_email' => $clientData['contact_email'] ?? '',
            'contact_phone' => $clientData['contact_phone'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'in_progress', // in_progress, completed
            'tasks' => [
                // المرحلة الأولى: التهيئة المحاسبية والبيانات الأساسية
                'doc_cr_vat' => false,
                'doc_national_address' => false,
                'chart_of_accounts' => false,
                'opening_balances' => false,
                'import_contacts_items' => false,

                // المرحلة الثانية: الربط الضريبي
                'zatca_otp_obtained' => false,
                'zatca_egs_registered' => false,
                'zatca_csid_compliance' => false,
                'zatca_invoice_template' => false,
                'zatca_b2b_clearance_test' => false,
                'zatca_b2c_reporting_test' => false,

                // المرحلة الثالثة: تكامل CRM مع Books
                'crm_books_connected' => false,
                'crm_field_mapping' => false,
                'crm_deluge_scripts_installed' => false,
                'user_roles_permissions' => false,

                // المرحلة الرابعة: التدريب والتسليم
                'training_session_conducted' => false,
                'user_manual_delivered' => false,
                'sla_channel_activated' => false,
            ]
        ];

        $clients[$id] = $newClient;
        $this->saveClients($clients);
        return $newClient;
    }

    /**
     * تحديث حالة مهمة لعميل
     */
    public function updateTaskStatus(string $clientId, string $taskKey, bool $status): bool
    {
        $clients = $this->getClients();
        if (!isset($clients[$clientId])) {
            return false;
        }

        $clients[$clientId]['tasks'][$taskKey] = $status;

        // فحص نسبة الإنجاز
        $total = count($clients[$clientId]['tasks']);
        $completed = count(array_filter($clients[$clientId]['tasks']));
        if ($completed === $total) {
            $clients[$clientId]['status'] = 'completed';
        } else {
            $clients[$clientId]['status'] = 'in_progress';
        }

        return $this->saveClients($clients);
    }

    /**
     * حساب نسبة إنجاز العميل
     */
    public static function calculateProgress(array $client): int
    {
        if (empty($client['tasks'])) {
            return 0;
        }
        $total = count($client['tasks']);
        $completed = count(array_filter($client['tasks']));
        return (int)round(($completed / $total) * 100);
    }

    /**
     * بيانات أولية توضيحية لعملاء في السعودية ودبي
     */
    private function seedInitialData(): void
    {
        $sampleClients = [
            'CLI-SA001' => [
                'id' => 'CLI-SA001',
                'name' => 'سلطان بن عبدالعزيز المنصور',
                'company' => 'شركة آفاق السحاب للاتصالات وتقنية المعلومات',
                'country' => 'SA',
                'vat_number' => '310123456700003',
                'cr_number' => '1010987654',
                'contact_email' => 'sultan@afaqcloud.com.sa',
                'contact_phone' => '+966505123456',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'status' => 'in_progress',
                'tasks' => [
                    'doc_cr_vat' => true,
                    'doc_national_address' => true,
                    'chart_of_accounts' => true,
                    'opening_balances' => true,
                    'import_contacts_items' => true,
                    'zatca_otp_obtained' => true,
                    'zatca_egs_registered' => true,
                    'zatca_csid_compliance' => true,
                    'zatca_invoice_template' => true,
                    'zatca_b2b_clearance_test' => true,
                    'zatca_b2c_reporting_test' => false,
                    'crm_books_connected' => true,
                    'crm_field_mapping' => true,
                    'crm_deluge_scripts_installed' => true,
                    'user_roles_permissions' => true,
                    'training_session_conducted' => false,
                    'user_manual_delivered' => true,
                    'sla_channel_activated' => false,
                ]
            ],
            'CLI-AE002' => [
                'id' => 'CLI-AE002',
                'name' => 'طارق راشد النعيمي',
                'company' => 'مؤسسة الواحة الذكية لحلول الأعمال - دبي',
                'country' => 'AE',
                'vat_number' => '100234567800003',
                'cr_number' => 'DXB-883921',
                'contact_email' => 'tariq@alwaha-dxb.ae',
                'contact_phone' => '+971501234890',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'status' => 'in_progress',
                'tasks' => [
                    'doc_cr_vat' => true,
                    'doc_national_address' => true,
                    'chart_of_accounts' => true,
                    'opening_balances' => false,
                    'import_contacts_items' => false,
                    'zatca_otp_obtained' => false,
                    'zatca_egs_registered' => false,
                    'zatca_csid_compliance' => false,
                    'zatca_invoice_template' => false,
                    'zatca_b2b_clearance_test' => false,
                    'zatca_b2c_reporting_test' => false,
                    'crm_books_connected' => true,
                    'crm_field_mapping' => true,
                    'crm_deluge_scripts_installed' => false,
                    'user_roles_permissions' => false,
                    'training_session_conducted' => false,
                    'user_manual_delivered' => false,
                    'sla_channel_activated' => false,
                ]
            ]
        ];

        $this->saveClients($sampleClients);
    }
}
