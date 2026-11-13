<?php
namespace App;

/**
 * ============================================================================
 * قوالب ونماذج تهيئة شجرة الحسابات واستيراد البيانات المحاسبية
 * Chart of Accounts & Master Data Templates (Saudi KSA & UAE)
 * ============================================================================
 *
 * المطور: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 */
class DataTemplates
{
    /**
     * شجرة الحسابات القياسية المتوافقة مع معايير المحاسبة وهيئة الزكاة (KSA SOCPA / ZATCA)
     */
    public static function getSaudiChartOfAccounts(): array
    {
        return [
            // الأصول (Assets)
            ['code' => '10101', 'name_ar' => 'الصندوق الرئيسي (النقدية)', 'name_en' => 'Main Cash on Hand', 'type' => 'Cash', 'parent' => 'الأصول المتداولة'],
            ['code' => '10102', 'name_ar' => 'البنك الأهلي السعودي (حساب جاري)', 'name_en' => 'SNB Bank Current Account', 'type' => 'Bank', 'parent' => 'الأصول المتداولة'],
            ['code' => '10103', 'name_ar' => 'مصرف الراجحي (حساب الشركات)', 'name_en' => 'Al Rajhi Corporate Account', 'type' => 'Bank', 'parent' => 'الأصول المتداولة'],
            ['code' => '10201', 'name_ar' => 'المدينون التجاريون (ذمم العملاء)', 'name_en' => 'Accounts Receivable', 'type' => 'Accounts Receivable', 'parent' => 'الأصول المتداولة'],
            ['code' => '10301', 'name_ar' => 'مخزون البضائع والمنتجات', 'name_en' => 'Inventory Asset', 'type' => 'Stock', 'parent' => 'الأصول المتداولة'],
            ['code' => '10401', 'name_ar' => 'أرصدة ضريبة القيمة المضافة المدخلات (مستردة 15%)', 'name_en' => 'Input VAT Paid (15%)', 'type' => 'Other Current Asset', 'parent' => 'الأصول المتداولة'],
            ['code' => '15101', 'name_ar' => 'أجهزة الكمبيوتر والخوادم السحابية', 'name_en' => 'IT Equipment & Hardware', 'type' => 'Fixed Asset', 'parent' => 'الأصول الثابتة'],
            ['code' => '15201', 'name_ar' => 'مجمع إهلاك الأصول الثابتة', 'name_en' => 'Accumulated Depreciation', 'type' => 'Fixed Asset', 'parent' => 'الأصول الثابتة'],

            // الخصوم (Liabilities)
            ['code' => '20101', 'name_ar' => 'الدائنون التجاريون (ذمم الموردين)', 'name_en' => 'Accounts Payable', 'type' => 'Accounts Payable', 'parent' => 'الخصوم المتداولة'],
            ['code' => '20201', 'name_ar' => 'أمانات ضريبة القيمة المضافة للمخرجات (المحصلة 15%)', 'name_en' => 'Output VAT Payable (15%)', 'type' => 'Other Current Liability', 'parent' => 'الخصوم المتداولة'],
            ['code' => '20301', 'name_ar' => 'مستحقات التأمينات الاجتماعية (GOSI)', 'name_en' => 'GOSI Payable', 'type' => 'Other Current Liability', 'parent' => 'الخصوم المتداولة'],
            ['code' => '20401', 'name_ar' => 'مخصص مكافأة نهاية الخدمة', 'name_en' => 'End of Service Indemnity Provision', 'type' => 'Other Non-Current Liability', 'parent' => 'الخصوم غير المتداولة'],

            // حقوق الملكية (Equity)
            ['code' => '30101', 'name_ar' => 'رأس المال المدفوع', 'name_en' => 'Paid-in Capital', 'type' => 'Equity', 'parent' => 'حقوق الملكية'],
            ['code' => '30201', 'name_ar' => 'الأرباح (الخسائر) المبقاة / المدورة', 'name_en' => 'Retained Earnings', 'type' => 'Equity', 'parent' => 'حقوق الملكية'],
            ['code' => '30301', 'name_ar' => 'جاري الشركاء / الملاك', 'name_en' => 'Owners Current Account', 'type' => 'Equity', 'parent' => 'حقوق الملكية'],

            // الإيرادات (Income)
            ['code' => '40101', 'name_ar' => 'إيرادات مبيعات وتراخيص البرمجيات والأنظمة', 'name_en' => 'Software & License Sales', 'type' => 'Income', 'parent' => 'إيرادات النشاط'],
            ['code' => '40201', 'name_ar' => 'إيرادات خدمات الاستشارات والربط والتهيئة', 'name_en' => 'Consulting & Implementation Services', 'type' => 'Income', 'parent' => 'إيرادات النشاط'],
            ['code' => '40301', 'name_ar' => 'إيرادات عقود الصيانة والدعم الفني السنوي', 'name_en' => 'Maintenance & SLA Support Income', 'type' => 'Income', 'parent' => 'إيرادات النشاط'],

            // المصروفات (Expenses)
            ['code' => '50101', 'name_ar' => 'تكلفة البضاعة المباعة والخدمات المقدمة', 'name_en' => 'Cost of Goods / Services Sold', 'type' => 'Cost of Goods Sold', 'parent' => 'تكاليف المبيعات'],
            ['code' => '60101', 'name_ar' => 'رواتب وأجور الموظفين', 'name_en' => 'Salaries & Wages Expense', 'type' => 'Expense', 'parent' => 'المصروفات العمومية والإدارية'],
            ['code' => '60102', 'name_ar' => 'بدلات سكن وانتقال الموظفين', 'name_en' => 'Housing & Transport Allowances', 'type' => 'Expense', 'parent' => 'المصروفات العمومية والإدارية'],
            ['code' => '60201', 'name_ar' => 'إيجار المكاتب ومقرات العمل', 'name_en' => 'Office Rent Expense', 'type' => 'Expense', 'parent' => 'المصروفات العمومية والإدارية'],
            ['code' => '60301', 'name_ar' => 'اشتراكات السيرفرات والبنية السحابية (AWS / Zoho)', 'name_en' => 'Cloud Servers & SaaS Subscriptions', 'type' => 'Expense', 'parent' => 'المصروفات التشغيلية'],
            ['code' => '60401', 'name_ar' => 'مصروفات التسويق والإعلانات الرقمية', 'name_en' => 'Advertising & Marketing Expense', 'type' => 'Expense', 'parent' => 'المصروفات التسويقية'],
            ['code' => '60501', 'name_ar' => 'رسوم بنكية ومصروفات بوابات الدفع الإلكتروني', 'name_en' => 'Bank Charges & Payment Gateway Fees', 'type' => 'Expense', 'parent' => 'المصروفات الإدارية'],
        ];
    }

    /**
     * شجرة الحسابات للشركات في دولة الإمارات (UAE FTA Standard)
     */
    public static function getUaeChartOfAccounts(): array
    {
        return [
            ['code' => '1010', 'name_ar' => 'النقد وما في حكمه', 'name_en' => 'Cash and Cash Equivalents', 'type' => 'Bank', 'parent' => 'Current Assets'],
            ['code' => '1020', 'name_ar' => 'المدينون (حسابات العملاء)', 'name_en' => 'Trade Receivables', 'type' => 'Accounts Receivable', 'parent' => 'Current Assets'],
            ['code' => '1050', 'name_ar' => 'ضريبة القيمة المضافة المستردة (5%)', 'name_en' => 'VAT Recoverable (5%)', 'type' => 'Other Current Asset', 'parent' => 'Current Assets'],
            ['code' => '2010', 'name_ar' => 'الدائنون (حسابات الموردين)', 'name_en' => 'Trade Payables', 'type' => 'Accounts Payable', 'parent' => 'Current Liabilities'],
            ['code' => '2050', 'name_ar' => 'ضريبة القيمة المضافة المستحقة (5%)', 'name_en' => 'VAT Output Payable (5%)', 'type' => 'Other Current Liability', 'parent' => 'Current Liabilities'],
            ['code' => '3010', 'name_ar' => 'رأس مال الشركة', 'name_en' => 'Share Capital', 'type' => 'Equity', 'parent' => 'Equity'],
            ['code' => '4010', 'name_ar' => 'إيرادات المبيعات الخاضعة للنسبة القياسية (5%)', 'name_en' => 'Standard Rated Sales (5%)', 'type' => 'Income', 'parent' => 'Revenue'],
            ['code' => '4020', 'name_ar' => 'إيرادات خاضعة للنسبة الصفرية / صادرات', 'name_en' => 'Zero Rated / Export Supplies', 'type' => 'Income', 'parent' => 'Revenue'],
            ['code' => '5010', 'name_ar' => 'تكاليف العمليات المباشرة', 'name_en' => 'Cost of Sales', 'type' => 'Cost of Goods Sold', 'parent' => 'Cost of Sales'],
            ['code' => '6010', 'name_ar' => 'المصروفات التشغيلية والرواتب', 'name_en' => 'General & Administrative Expenses', 'type' => 'Expense', 'parent' => 'Operating Expenses']
        ];
    }

    /**
     * نموذج استيراد جهات الاتصال (Contacts / Customers Import Template) لـ Zoho Books بصيغة CSV
     */
    public static function getCustomerImportCsvTemplate(): string
    {
        $headers = [
            'Display Name',
            'Company Name',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Mobile',
            'Currency Code',
            'VAT Treatment',
            'Tax Registration Number',
            'Billing Address',
            'Billing Street2',
            'Billing City',
            'Billing State',
            'Billing Country',
            'Billing Zip'
        ];

        $sampleRow1 = [
            'شركة آفاق التقنية المحدودة',
            'شركة آفاق التقنية المحدودة',
            'أحمد',
            'المنصور',
            'billing@afaq-tech.com.sa',
            '+966112345678',
            '+966501234567',
            'SAR',
            'vat_registered',
            '310123456700003',
            'طريق التخصصي، حي العليا',
            'مبنى 4523، الدور الثالث',
            'الرياض',
            'منطقة الرياض',
            'Saudi Arabia',
            '12334'
        ];

        $sampleRow2 = [
            'مؤسسة النور للتوريدات والتجارة',
            'مؤسسة النور للتوريدات',
            'سالم',
            'العتيبي',
            'info@alnoor-trade.sa',
            '+966126789012',
            '+966559876543',
            'SAR',
            'vat_registered',
            '300987654300003',
            'طريق المدينة المنورة',
            'مبنى 8812',
            'جدة',
            'منطقة مكة المكرمة',
            'Saudi Arabia',
            '21432'
        ];

        $output = implode(',', $headers) . "\n";
        $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $sampleRow1)) . "\n";
        $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $sampleRow2)) . "\n";

        return $output;
    }

    /**
     * نموذج استيراد الأصناف والخدمات (Items / Services Import Template) لـ Zoho Books بصيغة CSV
     */
    public static function getItemsImportCsvTemplate(): string
    {
        $headers = [
            'Item Name',
            'SKU',
            'Description',
            'Rate',
            'Account',
            'Item Type',
            'Tax Percentage',
            'Tax Name',
            'Product Type',
            'Purchase Rate',
            'Purchase Account'
        ];

        $sampleRow1 = [
            'خدمات تهيئة وربط Zoho Books مع ZATCA',
            'SRV-ZATCA-01',
            'تهيئة النظام واستخراج شهادات CSID وتفعيل المرحلة الثانية من الفوترة الإلكترونية',
            '3500.00',
            'إيرادات خدمات الاستشارات والربط والتهيئة',
            'service',
            '15',
            'ضريبة القيمة المضافة القياسية (15%)',
            'service',
            '0.00',
            'تكلفة البضاعة المباعة والخدمات المقدمة'
        ];

        $sampleRow2 = [
            'عقد صيانة ودعم فني سنوي لأنظمة Zoho (SLA)',
            'SLA-ANNUAL-01',
            'دعم فني واستجابة خلال ساعتين لحالات التوقف وتحديثات الضرائب الدورية',
            '6000.00',
            'إيرادات عقود الصيانة والدعم الفني السنوي',
            'service',
            '15',
            'ضريبة القيمة المضافة القياسية (15%)',
            'service',
            '0.00',
            'تكلفة البضاعة المباعة والخدمات المقدمة'
        ];

        $sampleRow3 = [
            'جهاز نقطة بيع لوحي مخصص للفوترة السحابية',
            'HW-POS-TAB-10',
            'جهاز تابلت شاشة 10 بوصة متوافق مع طابعات الإيصالات الحرارية لـ ZATCA',
            '1800.00',
            'إيرادات مبيعات وتراخيص البرمجيات والأنظمة',
            'inventory',
            '15',
            'ضريبة القيمة المضافة القياسية (15%)',
            'goods',
            '1200.00',
            'تكلفة البضاعة المباعة والخدمات المقدمة'
        ];

        $output = implode(',', $headers) . "\n";
        $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $sampleRow1)) . "\n";
        $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $sampleRow2)) . "\n";
        $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $sampleRow3)) . "\n";

        return $output;
    }

    /**
     * نموذج قيد الأرصدة الافتتاحية (Opening Balances Journal Entry Template)
     */
    public static function getOpeningBalancesJournalTemplate(): string
    {
        $headers = [
            'Date',
            'Journal Number',
            'Account Code',
            'Account Name',
            'Debit',
            'Credit',
            'Notes',
            'Currency'
        ];

        $rows = [
            ['2026-01-01', 'OB-2026-001', '10102', 'البنك الأهلي السعودي (حساب جاري)', '150000.00', '0.00', 'الرصيد الافتتاحي للبنك', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '10201', 'المدينون التجاريون (ذمم العملاء)', '65000.00', '0.00', 'أرصدة فواتير مستحقة من العام السابق', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '10301', 'مخزون البضائع والمنتجات', '45000.00', '0.00', 'بضاعة آخر المدة للعام السابق', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '15101', 'أجهزة الكمبيوتر والخوادم', '25000.00', '0.00', 'القيمة الدفترية للأصول الثابتة', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '20101', 'الدائنون التجاريون (ذمم الموردين)', '0.00', '35000.00', 'مستحقات الموردين المؤجلة', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '30101', 'رأس المال المدفوع', '0.00', '200000.00', 'رأس المال المسجل بالسجل التجاري', 'SAR'],
            ['2026-01-01', 'OB-2026-001', '30201', 'الأرباح المبقاة / المدورة', '0.00', '50000.00', 'أرباح مدورة من الفترات المالية السابقة', 'SAR'],
        ];

        $output = implode(',', $headers) . "\n";
        foreach ($rows as $row) {
            $output .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }
        return $output;
    }
}
