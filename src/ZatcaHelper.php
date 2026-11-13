<?php
namespace App;

/**
 * فئة مساعدة لتوليد رمز الاستجابة السريع (QR Code) المتوافق مع متطلبات ZATCA (TLV Encoding Base64)
 * والتحقق من صحة الأرقام الضريبية في السعودية والإمارات
 */
class ZatcaHelper
{
    /**
     * تحويل حقل إلى ترميز TLV (Tag-Length-Value)
     * Tag: 1 byte
     * Length: 1 byte
     * Value: UTF-8 string bytes
     */
    private static function toTlv(int $tag, string $value): string
    {
        $valueLength = strlen($value);
        return chr($tag) . chr($valueLength) . $value;
    }

    /**
     * توليد رمز QR متوافق مع هيئة الزكاة والضريبة والجمارك (المرحلة 1 و 2 للفاتورة المبسطة)
     * Tags:
     * 1: اسم المورد
     * 2: الرقم الضريبي للمورد
     * 3: وقت وتاريخ الفاتورة (ISO 8601)
     * 4: إجمالي الفاتورة مع الضريبة
     * 5: إجمالي قيمة الضريبة
     * 6: هاش الفاتورة (اختياري / مرحلة 2)
     * 7: التوقيع الرقمي ECDSA (اختياري / مرحلة 2)
     */
    public static function generateQrBase64(
        string $sellerName,
        string $vatNumber,
        string $invoiceTimestamp,
        float $totalAmount,
        float $taxAmount,
        string $invoiceHash = '',
        string $ecdsaSignature = ''
    ): string {
        $tlv = self::toTlv(1, $sellerName);
        $tlv .= self::toTlv(2, $vatNumber);
        $tlv .= self::toTlv(3, $invoiceTimestamp);
        $tlv .= self::toTlv(4, number_format($totalAmount, 2, '.', ''));
        $tlv .= self::toTlv(5, number_format($taxAmount, 2, '.', ''));

        if (!empty($invoiceHash)) {
            $tlv .= self::toTlv(6, $invoiceHash);
        }
        if (!empty($ecdsaSignature)) {
            $tlv .= self::toTlv(7, $ecdsaSignature);
        }

        return base64_encode($tlv);
    }

    /**
     * فحص وتدقيق الرقم الضريبي السعودي (15 رقماً يبدأ بـ 3 وينتهي بـ 3)
     */
    public static function validateSaudiVat(string $vatNumber): array
    {
        $cleanVat = trim($vatNumber);
        if (!preg_match('/^[0-9]{15}$/', $cleanVat)) {
            return [
                'valid' => false,
                'message' => 'الرقم الضريبي السعودي يجب أن يتكون من 15 رقماً بالتمام والكمال.'
            ];
        }

        if ($cleanVat[0] !== '3' || $cleanVat[14] !== '3') {
            return [
                'valid' => false,
                'message' => 'الرقم الضريبي السعودي للمنشآت الخاضعة يجب أن يبدأ بـ 3 وينتهي بـ 3.'
            ];
        }

        return [
            'valid' => true,
            'vat' => $cleanVat,
            'message' => 'الرقم الضريبي متوافق وصحيح طبقاً لاشتراطات ZATCA.'
        ];
    }

    /**
     * فحص وتدقيق الرقم الضريبي الإماراتي (TRN - 15 رقماً)
     */
    public static function validateUaeTrn(string $trnNumber): array
    {
        $cleanTrn = trim($trnNumber);
        if (!preg_match('/^[0-9]{15}$/', $cleanTrn)) {
            return [
                'valid' => false,
                'message' => 'رقم التسجيل الضريبي الإماراتي (TRN) يجب أن يتكون من 15 رقماً.'
            ];
        }

        if (!str_starts_with($cleanTrn, '100')) {
            return [
                'valid' => false,
                'message' => 'الرقم الضريبي الإماراتي يبدأ عادة بالبادئة 100.'
            ];
        }

        return [
            'valid' => true,
            'trn' => $cleanTrn,
            'message' => 'رقم التسجيل الضريبي الإماراتي (TRN) صحيح.'
        ];
    }
}
