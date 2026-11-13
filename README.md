# منظومة تنفيذ وصيانة Zoho Books و Zoho CRM والربط الضريبي
**خاص بشركة العواضي للحلول السحابية (Alawadi Cloud Solutions)**

---

##  نظرة عامة على المشروع
تم بناء هذه المنظومة بالكامل لتكون الحل التقني والمتكامل لتنفيذ وتهيئة وصيانة أنظمة **Zoho Books** و **Zoho CRM**، مع تفعيل الربط الضريبي المعتمد من **هيئة الزكاة والضريبة والجمارك (ZATCA)** في المملكة العربية السعودية، والهيئة الاتحادية للضرائب في الإمارات (FTA)، وإدارة تسليم وتدشين العملاء واتفاقيات مستوى الخدمة (SLA).

---

##  محتويات المنظومة ومكوناتها المكتملة:

### 1. لوحة القيادة المتكاملة ([`index.php`](file:///C:/xampp/htdocs/Zohoobooks/index.php))
- **رابط التشغيل المحلي:** `http://localhost/Zohoobooks/index.php`
- **مولد رمز الاستجابة السريع لـ ZATCA:** توليد رموز QR المشفرة بتقنية **TLV (Tag-Length-Value) Base64** المعتمدة للمرحلة الثانية من الفوترة الإلكترونية، وعرض الكود المشفر ومسحه تجريبياً عبر أي هاتف.
- **فاحص ومدقق الأرقام الضريبية (VAT / TRN Validator):** فحص أرقام التسجيل الضريبي للشركات السعودية (15 خانة تبدأ بـ 3 وتنتهي بـ 3) والشركات الإماراتية قبل إدخالها للنظام لضمان عدم رفض الفواتير.
- اختصارات سريعة لجميع الأدوات التفاعلية ولوحات المتابعة.

### 2. محرك ومحاكي فواتير ZATCA الحية ([`zatca_simulator.php`](file:///C:/xampp/htdocs/Zohoobooks/zatca_simulator.php) & [`src/ZatcaClient.php`](file:///C:/xampp/htdocs/Zohoobooks/src/ZatcaClient.php))
- توليد ملفات الفواتير الإلكترونية القياسية بصيغة **UBL 2.1 XML**.
- حساب الهاش الرقمي المشفر بالفاتورة (SHA-256 Digest) وربطه مع الـ Previous Invoice Hash (PIH).
- محاكاة واختبار إرسال واعتماد فواتير الشركات **B2B (Clearance)** وفواتير الأفراد **B2C (Reporting)** مع بوابات الهيئة.
- دوال الربط الميداني لاستخراج شهادات الامتثال (Compliance CSID) باستخدام رمز الـ OTP وشهادة الإنتاج (Production CSID).

### 3. لوحة إدارة وتدشين العملاء والمشاريع ([`onboarding_dashboard.php`](file:///C:/xampp/htdocs/Zohoobooks/onboarding_dashboard.php) & [`src/OnboardingManager.php`](file:///C:/xampp/htdocs/Zohoobooks/src/OnboardingManager.php))
- **رابط التشغيل المحلي:** `http://localhost/Zohoobooks/onboarding_dashboard.php`
- نظام متابعة تفاعلي يغطي **17 مهمة تفصيلية** لكل عميل موزع على 4 مراحل:
  1. مرحلة التهيئة المحاسبية والوثائق.
  2. مرحلة الربط الضريبي وهيئة الزكاة.
  3. مرحلة تكامل Zoho CRM وسكريبتات Deluge.
  4. مرحلة التدريب والتسليم والدعم.
- حساب نسبة الإنجاز المئوية وتحديث الحالات فورياً.
- زر لإضافة منشآت وعملاء جدد بنقرة واحدة.

### 4. نماذج وقوالب استيراد البيانات المحاسبية ([`src/DataTemplates.php`](file:///C:/xampp/htdocs/Zohoobooks/src/DataTemplates.php))
- نماذج جاهزة للتنزيل المباشر بصيغة **CSV**:
  - نموذج دليل العملاء وجهات الاتصال (`Zoho_Books_Customers_Import_Template.csv`).
  - نموذج دليل الأصناف والخدمات والأسعار (`Zoho_Books_Items_Import_Template.csv`).
  - نموذج قيد الأرصدة الافتتاحية وميزان المراجعة (`Zoho_Books_Opening_Balances_Journal.csv`).
- شجرة حسابات قياسية معتمدة للسعودية (SOCPA) والإمارات (FTA).

### 5. بوابة الصيانة والدعم الفني السريع وطوارئ الفوترة ([`sla_maintenance_portal.php`](file:///C:/xampp/htdocs/Zohoobooks/sla_maintenance_portal.php))
- **رابط التشغيل المحلي:** `http://localhost/Zohoobooks/sla_maintenance_portal.php`
- نظام تسجيل ومتابعة تذاكر الصيانة الطارئة وفق مستويات اتفاقية الخدمة (SLA):
  - **Critical (خلال 30 دقيقة):** لحالات توقف الفوترة أو رفض فواتير ZATCA.
  - **High (خلال ساعتين):** لتوقف مزامنة CRM أو أخطاء المحاسبة.
  - **Normal (خلال 12-24 ساعة):** للاستفسارات وتعديل القوالب والتقارير.

### 6. دليل تشغيل وتدريب المستخدم والمحاسبين ([`USER_TRAINING_MANUAL.md`](file:///C:/xampp/htdocs/Zohoobooks/USER_TRAINING_MANUAL.md))
- دليل عملي لفريق عمل العميل يشرح:
  - دورة إصدار الفواتير القياسية (B2B) والمبسطة (B2C).
  - كيفية التعامل المحاسبي النظامي مع الإشعارات الدائنة والمدينة (Credit/Debit Notes) لتجنب كسر تسلسل الهاش.
  - آلية المزامنة التلقائية مع Zoho CRM عند إغلاق الصفقات (Closed Won).
  - استخراج تقارير إقرار القيمة المضافة (KSA VAT & UAE 201).

### 7. الأدلة وسكربتات Deluge والمصادقة:
- [**`PROPOSAL_MOSTAQL.md`**](file:///C:/xampp/htdocs/Zohoobooks/PROPOSAL_MOSTAQL.md): العرض الاحترافي الجاهز للتقديم على منصة مستقل.
- [**`ZATCA_ZOHO_INTEGRATION_GUIDE.md`**](file:///C:/xampp/htdocs/Zohoobooks/ZATCA_ZOHO_INTEGRATION_GUIDE.md): دليل خطوات الربط مع بوابة فاتورة واستخراج الـ OTP والـ CSID.
- [**`ZOHO_CRM_BOOKS_DELUGE_SCRIPTS.md`**](file:///C:/xampp/htdocs/Zohoobooks/ZOHO_CRM_BOOKS_DELUGE_SCRIPTS.md): سكريبتات Deluge البرمجية لأتمتة تحويل الصفقات إلى فواتير وتحديث حالات السداد عبر Webhooks.
- [**`CLIENT_ONBOARDING_CHECKLIST.md`**](file:///C:/xampp/htdocs/Zohoobooks/CLIENT_ONBOARDING_CHECKLIST.md): قائمة فحص تدشين العملاء.
- [**`config.php`**](file:///C:/xampp/htdocs/Zohoobooks/config.php): ملف إعدادات الربط ومفاتيح API موثق بالكامل.
- [**`oauth_callback.php`**](file:///C:/xampp/htdocs/Zohoobooks/oauth_callback.php): بوابة التوثيق والمصادقة التلقائية لـ OAuth 2.0.

---

##  طريقة الاستخدام والتشغيل:
1. تأكد من تشغيل Apache من خلال لوحة تحكم **XAMPP**.
2. افتح المتصفح على أي من الروابط التالية:
   - **لوحة التحكم الرئيسية:** `http://localhost/Zohoobooks/`
   - **لوحة تدشين العملاء (Onboarding):** `http://localhost/Zohoobooks/onboarding_dashboard.php`
   - **محاكي فواتير ZATCA UBL 2.1:** `http://localhost/Zohoobooks/zatca_simulator.php`
   - **بوابة طوارئ الصيانة والدعم (SLA):** `http://localhost/Zohoobooks/sla_maintenance_portal.php`
