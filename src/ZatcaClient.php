<?php
namespace App;

/**
 * ============================================================================
 * عميل الربط مع بوابة هيئة الزكاة والضريبة والجمارك (ZATCA Fatoora API Client)
 * ============================================================================
 *
 * المطور: شركة العواضي للحلول السحابية (Alawadi Cloud Solutions)
 * الوظائف الرئيسية:
 * 1. تسجيل وحدة توليد الفواتير (EGS Onboarding) واستخراج Compliance CSID عبر رمز OTP.
 * 2. طلب شهادة الإنتاج الحية (Production CSID).
 * 3. فحص الامتثال لفواتير B2B (Clearance) وفواتير B2C (Reporting).
 * 4. إنشاء وتجهيز فاتورة بصيغة UBL 2.1 XML قياسية مطابقة لاشتراطات الهيئة.
 * 5. حساب هاش الفاتورة (SHA-256 Digest) وربط الفواتير المتعاقبة (Previous Invoice Hash - PIH).
 */
class ZatcaClient
{
    private array $config;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->config = $config['zatca'] ?? [];
        $env = $this->config['environment'] ?? 'sandbox';
        $this->baseUrl = ($env === 'production')
            ? ($this->config['production_url'] ?? 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core')
            : ($this->config['sandbox_url'] ?? 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation');
    }

    /**
     * الحصول على الرابط الحالي للبيئة النشطة
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * تسجيل وحدة توليد الفواتير والحصول على شهادة الامتثال (Compliance CSID)
     * باستخدام رمز OTP الصادر من منصة فاتورة وطلب توقيع الشهادة (CSR).
     *
     * @param string $csrBase64 كود الـ CSR مشفراً بصيغة Base64
     * @param string $otpCode   رمز المرور المؤقت لمرة واحدة (OTP من بوابة فاتورة)
     * @return array نتيجة الطلب متضمنة binarySecurityToken و secret
     */
    public function requestComplianceCsid(string $csrBase64, string $otpCode): array
    {
        $endpoint = $this->baseUrl . '/compliance';

        $headers = [
            'accept: application/json',
            'Accept-Version: V2',
            'OTP: ' . trim($otpCode),
            'Content-Type: application/json'
        ];

        $payload = [
            'csr' => $csrBase64
        ];

        return $this->executeCurl($endpoint, 'POST', $headers, $payload);
    }

    /**
     * إصدار شهادة الإنتاج الحية (Production CSID)
     * بعد نجاح فحص الامتثال.
     *
     * @param string $complianceRequestId معرف طلب الامتثال
     * @param string $binarySecurityToken رمز الأمان من الخطوة السابقة
     * @param string $secret              المفتاح السري
     * @return array
     */
    public function requestProductionCsid(string $complianceRequestId, string $binarySecurityToken, string $secret): array
    {
        $endpoint = $this->baseUrl . '/production/csids';

        $authHeader = 'Basic ' . base64_encode($binarySecurityToken . ':' . $secret);
        $headers = [
            'accept: application/json',
            'Accept-Version: V2',
            'Authorization: ' . $authHeader,
            'Content-Type: application/json'
        ];

        $payload = [
            'compliance_request_id' => $complianceRequestId
        ];

        return $this->executeCurl($endpoint, 'POST', $headers, $payload);
    }

    /**
     * إرسال الفاتورة للاعتماد المسبق (B2B Clearance API)
     *
     * @param string $invoiceXmlBase64 الفاتورة بصيغة UBL 2.1 XML مشفرة Base64
     * @param string $invoiceHash      هاش الفاتورة SHA-256 مشفر Base64
     * @param string $uuid             المعرف الفريد العالمي للفاتورة
     * @param string $token            التوكن الرقمي (CSID)
     * @param string $secret           المفتاح السري
     * @return array
     */
    public function clearInvoice(string $invoiceXmlBase64, string $invoiceHash, string $uuid, string $token, string $secret): array
    {
        $endpoint = $this->baseUrl . '/invoices/clearance/single';

        $authHeader = 'Basic ' . base64_encode($token . ':' . $secret);
        $headers = [
            'accept: application/json',
            'Accept-Version: V2',
            'Authorization: ' . $authHeader,
            'Clearance-Status: 1',
            'Content-Type: application/json'
        ];

        $payload = [
            'invoiceHash' => $invoiceHash,
            'uuid' => $uuid,
            'invoice' => $invoiceXmlBase64
        ];

        return $this->executeCurl($endpoint, 'POST', $headers, $payload);
    }

    /**
     * إرسال الفاتورة المبسطة للإبلاغ (B2C Reporting API)
     *
     * @param string $invoiceXmlBase64
     * @param string $invoiceHash
     * @param string $uuid
     * @param string $token
     * @param string $secret
     * @return array
     */
    public function reportInvoice(string $invoiceXmlBase64, string $invoiceHash, string $uuid, string $token, string $secret): array
    {
        $endpoint = $this->baseUrl . '/invoices/reporting/single';

        $authHeader = 'Basic ' . base64_encode($token . ':' . $secret);
        $headers = [
            'accept: application/json',
            'Accept-Version: V2',
            'Authorization: ' . $authHeader,
            'Content-Type: application/json'
        ];

        $payload = [
            'invoiceHash' => $invoiceHash,
            'uuid' => $uuid,
            'invoice' => $invoiceXmlBase64
        ];

        return $this->executeCurl($endpoint, 'POST', $headers, $payload);
    }

    /**
     * إنشاء هيكل فاتورة إلكترونية بصيغة UBL 2.1 XML القياسية والمتوافقة مع اشتراطات ZATCA
     */
    public static function buildSampleUblXml(array $data): string
    {
        $uuid = $data['uuid'] ?? self::generateUuidV4();
        $invoiceNumber = $data['invoice_number'] ?? 'INV-' . date('Ymd-His');
        $issueDate = $data['issue_date'] ?? date('Y-m-d');
        $issueTime = $data['issue_time'] ?? date('H:i:s');
        $invoiceType = $data['invoice_type'] ?? '388'; // 388 = Tax Invoice
        $subType = $data['subtype'] ?? '0100000'; // 0100000 = Standard (B2B), 0200000 = Simplified (B2C)

        $sellerName = htmlspecialchars($data['seller_name'] ?? 'شركة العواضي للحلول السحابية');
        $sellerVat = $data['seller_vat'] ?? '300000000000003';
        $sellerStreet = htmlspecialchars($data['seller_street'] ?? 'طريق الملك فهد');
        $sellerBuilding = $data['seller_building'] ?? '1234';
        $sellerPostal = $data['seller_postal'] ?? '12345';
        $sellerCity = htmlspecialchars($data['seller_city'] ?? 'الرياض');
        $sellerDistrict = htmlspecialchars($data['seller_district'] ?? 'العليا');

        $buyerName = htmlspecialchars($data['buyer_name'] ?? 'شركة العميل التجاري');
        $buyerVat = $data['buyer_vat'] ?? '310999999900003';

        $lineItemName = htmlspecialchars($data['item_name'] ?? 'خدمات استشارات سحابية وتنفيذ أنظمة Zoho');
        $lineNet = (float)($data['net_amount'] ?? 1000.00);
        $taxRate = 15.0;
        $taxAmount = round($lineNet * ($taxRate / 100), 2);
        $totalWithTax = round($lineNet + $taxAmount, 2);

        $pih = $data['pih'] ?? 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMjRiMWM2YzNlMzFhYzVkMzAzMmJjOGQ3NTI4NzfcMQ==';

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2">
    <cbc:ProfileID>reporting:1.0</cbc:ProfileID>
    <cbc:ID>{$invoiceNumber}</cbc:ID>
    <cbc:UUID>{$uuid}</cbc:UUID>
    <cbc:IssueDate>{$issueDate}</cbc:IssueDate>
    <cbc:IssueTime>{$issueTime}</cbc:IssueTime>
    <cbc:InvoiceTypeCode name="{$subType}">{$invoiceType}</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>SAR</cbc:DocumentCurrencyCode>
    <cbc:TaxCurrencyCode>SAR</cbc:TaxCurrencyCode>
    <cac:AdditionalDocumentReference>
        <cbc:ID>PIH</cbc:ID>
        <cac:Attachment>
            <cac:EmbeddedDocumentBinaryObject mimeCode="text/plain">{$pih}</cac:EmbeddedDocumentBinaryObject>
        </cac:Attachment>
    </cac:AdditionalDocumentReference>
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="CRN">1010123456</cbc:ID>
            </cac:PartyIdentification>
            <cac:PostalAddress>
                <cbc:StreetName>{$sellerStreet}</cbc:StreetName>
                <cbc:BuildingNumber>{$sellerBuilding}</cbc:BuildingNumber>
                <cbc:CitySubdivisionName>{$sellerDistrict}</cbc:CitySubdivisionName>
                <cbc:CityName>{$sellerCity}</cbc:CityName>
                <cbc:PostalZone>{$sellerPostal}</cbc:PostalZone>
                <cac:Country>
                    <cbc:IdentificationCode>SA</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>{$sellerVat}</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>{$sellerName}</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="NAT">1020304050</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>{$buyerVat}</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>{$buyerName}</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="SAR">{$taxAmount}</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="SAR">{$lineNet}</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="SAR">{$taxAmount}</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>{$taxRate}</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="SAR">{$lineNet}</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="SAR">{$lineNet}</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="SAR">{$totalWithTax}</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="SAR">{$totalWithTax}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:InvoicedQuantity unitCode="PCE">1.00</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="SAR">{$lineNet}</cbc:LineExtensionAmount>
        <cac:Item>
            <cbc:Name>{$lineItemName}</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>{$taxRate}</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="SAR">{$lineNet}</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>
</Invoice>
XML;

        return trim($xml);
    }

    /**
     * حساب هاش الفاتورة (Invoice Digest / Hash) باستخدام SHA-256
     */
    public static function calculateInvoiceHash(string $xmlContent): string
    {
        $hashBinary = hash('sha256', $xmlContent, true);
        return base64_encode($hashBinary);
    }

    /**
     * توليد معرف فريد UUID V4
     */
    public static function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * تنفيذ طلب HTTP عبر cURL
     */
    private function executeCurl(string $url, string $method, array $headers, array $payload = []): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'status_code' => $httpCode,
                'error' => 'cURL Error: ' . $curlError
            ];
        }

        $json = json_decode($response, true);
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'status_code' => $httpCode,
            'response' => $json ?: $response
        ];
    }
}
