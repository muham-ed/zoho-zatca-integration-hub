<?php
/**
 * ============================================================================
 * ملف إعدادات الربط مع منصة Zoho وهيئة الزكاة والضريبة والجمارك (ZATCA)
 * Configuration File: Zoho APIs & ZATCA Integration
 * ============================================================================
 *
 * المطور / الجهة: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 * التطبيق: Alawadi Zoho & ZATCA Integration Hub
 *
 * الوصف (Description):
 * يحتوي هذا الملف على جميع المتغيرات والمفاتيح الأساسية للربط البرمجي:
 * 1. بيانات التعريف العامة بالتطبيق والبلد الافتراضي للضريبة (السعودية والإمارات).
 * 2. بيانات المصادقة مع منصات Zoho (Books & CRM) عبر بروتوكول OAuth 2.0.
 * 3. روابط بيئات هيئة الزكاة والضريبة والجمارك (ZATCA Fatoora Platform) للمرحلة الثانية.
 *
 *
 * تعليمات الأمان (Security Instructions):
 * - يجب الحفاظ على سرية مفاتيح الربط (Client Secret, Refresh Token).
 * - يُنصح بعدم رفع هذا الملف بقيمه الحقيقية إلى مستودعات Git العامة (Public Repositories).
 * - في بيئة الإنتاج، يُفضّل جلب القيم الحساسة عبر متغيرات البيئة (Environment Variables / .env).
 *
 * @package   Alawadi\ZohoZatcaHub
 * @author    Alawadi Cloud Solutions
 * @version   1.0.0
 * @license   Proprietary
 */

return [
    /**
     * --------------------------------------------------------------------------
     * إعدادات التطبيق العامة (General Application Settings)
     * --------------------------------------------------------------------------
     *
     * app_name        : اسم المنظومة الذي يظهر في الواجهات ولوحة التحكم.
     * version         : الإصدار الحالي للنظام.
     * default_country : رمز الدولة الافتراضية للتحقق من أرقام التسجيل الضريبي وتطبيق القواعد.
     *                   - 'SA' : المملكة العربية السعودية (ضريبة القيمة المضافة 15%).
     *                   - 'AE' : دولة الإمارات العربية المتحدة (ضريبة القيمة المضافة 5%).
     */
    'app_name' => 'Alawadi Zoho & ZATCA Integration Hub',
    'version' => '1.0.0',
    'default_country' => 'SA',

    /**
     * --------------------------------------------------------------------------
     * إعدادات الربط مع Zoho APIs (OAuth 2.0 Settings)
     * --------------------------------------------------------------------------
     *
     * لإعداد وتوليد بيانات الربط:
     * 1. ادخل إلى Zoho Developer Console: https://api-console.zoho.com
     * 2. أنشئ تطبيقاً جديداً بنوع "Server-based Applications".
     * 3. انسخ Client ID و Client Secret وضعهما في الحقول أدناه.
     * 4. حدد Redirect URI ليتطابق تماماً مع المسار المعتمد في منصة المطورين.
     */
    'zoho' => [
        /**
         * مُعرّف العميل (Client ID)
         * يُستخرج من لوحة تحكم مطوري Zoho (Zoho Developer Console).
         * @var string
         */
        'client_id' => '1000.69X3I9YORJHFD6HG4Y3JFYTI0M2ZEG',

        /**
         * المفتاح السري للعميل (Client Secret)
         * مفتاح سري عالي الحساسية يُستخدم لتأكيد هوية التطبيق عند طلب وتجديد التوكن.
         * @var string
         */
        'client_secret' => '002396411214c0000cb9e35cc3749890708f003501',

        /**
         * رابط إعادة التوجيه المعتمد (Authorized Redirect URI)
         * الرابط الذي تعيد Zoho توجيه المستخدم إليه حاملاً كود التفويض (Grant Code).
         * يجب أن يطابق بدقة ما تم إدخاله في Zoho API Console.
         * مثال محلي: 'http://localhost/Zohoobooks/oauth_callback.php'
         * @var string
         */
        'redirect_uri' => 'http://localhost/Zohoobooks/oauth_callback.php',

        /**
         * رمز التجديد الدائم (OAuth 2.0 Refresh Token)
         * رمز دائم يُستخدم للحصول على Access Token جديد تلقائياً عند انتهاء صلاحيته.
         * يتم الحصول عليه عند أول تفويض للتطبيق باستخدام Grant Code.
         * @var string
         */
        'refresh_token' => '1000.fe089960f7705c4b0873708392660bb2.6c795e554ed282d78fd807c6298b341d',

        /**
         * مُعرّف المنظمة في Zoho Books (Organization ID)
         * رقم التعريف الفريد لمنشأتك/شركتك في Zoho Books.
         * يمكن إيجاده في Zoho Books بالانتقال إلى:
         * Settings -> Organization Profile -> Organization ID (أعلى الصفحة).
         * @var string
         */
        'organization_id' => '',

        /**
         * خادم المصادقة والحسابات (Zoho Accounts Server)
         * يعتمد على نطاق مركز البيانات (Data Center) الذي تم تسجيل حسابك فيه:
         * - الحسابات العالمية (US)   : https://accounts.zoho.com
         * - الحسابات السعودية (SA)   : https://accounts.zoho.sa
         * - الحسابات الأوروبية (EU)   : https://accounts.zoho.eu
         * - الحسابات الهندية (IN)     : https://accounts.zoho.in
         * - الحسابات الأسترالية (AU) : https://accounts.zoho.com.au
         * @var string
         */
        'accounts_server' => 'https://accounts.zoho.com',

        /**
         * نطاق واجهة برمجة تطبيقات دفاتر زوهو (Zoho Books API Base Domain)
         * المسار الأساسي لطلبات استدعاء الفواتير والجهات المحاسبية في Zoho Books v3:
         * - النطاق العالمي (US) : https://www.zohoapis.com/books/v3
         * - النطاق السعودي (SA) : https://www.zohoapis.sa/books/v3
         * - النطاق الأوروبي (EU) : https://www.zohoapis.eu/books/v3
         * @var string
         */
        'books_api_domain' => 'https://www.zohoapis.com/books/v3',

        /**
         * نطاق واجهة برمجة تطبيقات إدارة علاقات العملاء (Zoho CRM API Base Domain)
         * المسار الأساسي لطلبات إدارة الصفقات، العملاء، والوحدات في Zoho CRM v3:
         * - النطاق العالمي (US) : https://www.zohoapis.com/crm/v3
         * - النطاق السعودي (SA) : https://www.zohoapis.sa/crm/v3
         * - النطاق الأوروبي (EU) : https://www.zohoapis.eu/crm/v3
         * @var string
         */
        'crm_api_domain' => 'https://www.zohoapis.com/crm/v3',
    ],

    /**
     * --------------------------------------------------------------------------
     * إعدادات هيئة الزكاة والضريبة والجمارك (ZATCA Fatoora Platform)
     * --------------------------------------------------------------------------
     *
     * المرحلة الثانية من الفوترة الإلكترونية (مرحلة الربط والتكامل - Integration Phase):
     * تشمل إرسال الفواتير بصيغة XML وتوليد ختم التشفير ورمز الاستجابة السريع (TLV QR Code).
     */
    'zatca' => [
        /**
         * بيئة التشغيل النشطة (Active Environment)
         * الخيارات المتاحة:
         * - 'sandbox'    : بيئة المحاكاة والتجارب (Simulation / Testing).
         * - 'production' : البيئة الفعلية والإنتاجية (Live Core Portal).
         * @var string
         */
        'environment' => 'sandbox',

        /**
         * رابط بوابة المحاكاة والفحص (ZATCA Simulation Endpoint)
         * تُستخدم للاختبارات وشهادات الامتثال (Compliance CSID) قبل الانتقال للإنتاج.
         * @var string
         */
        'sandbox_url' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation',

        /**
         * رابط بوابة الإنتاج المعتمدة (ZATCA Production Core Endpoint)
         * المنصة الحية لإصدار وإرسال واعتماد الفواتير الضريبية وتدقيق الفواتير المبسطة.
         * @var string
         */
        'production_url' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core',
    ]
];
