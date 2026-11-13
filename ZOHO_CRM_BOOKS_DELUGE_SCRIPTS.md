# سكربتات Deluge لتكامل Zoho CRM مع Zoho Books
**أتمتة العمليات ومزامنة البيانات بين المبيعات وإدارة الحسابات**

---

## 1. سكريبت تحويل الصفقة (Closed Won Deal) إلى فاتورة تلقائية في Zoho Books

```deluge
// اسم الوظيفة: createBooksInvoiceFromDeal
// المدخلات: dealId (Bigint)

dealDetails = zoho.crm.getRecordById("Deals", dealId);
accountId = dealDetails.get("Account_Name").get("id");
accountDetails = zoho.crm.getRecordById("Accounts", accountId);

// 1. استخراج بيانات العميل الضريبية
contactName = accountDetails.get("Account_Name");
vatNumber = accountDetails.get("VAT_Number");
customerEmail = accountDetails.get("Email");

// 2. البحث عن العميل في Zoho Books أو إنشائه
searchCustomer = zoho.books.getRecords("Contacts", "YOUR_BOOKS_ORG_ID", "zcrm_account_id=" + accountId);

booksCustomerId = "";
if(searchCustomer.size() > 0)
{
    booksCustomerId = searchCustomer.get(0).get("contact_id");
}
else
{
    // إنشاء عميل جديد في Zoho Books
    newContactMap = Map();
    newContactMap.put("contact_name", contactName);
    newContactMap.put("company_name", contactName);
    newContactMap.put("zcrm_account_id", accountId);
    newContactMap.put("tax_treatment", "vat_registered");
    newContactMap.put("tax_reg_no", vatNumber);
    newContactMap.put("currency_code", "SAR");
    
    createContactResp = zoho.books.createRecord("Contacts", "YOUR_BOOKS_ORG_ID", newContactMap);
    if(createContactResp.get("code") == 0)
    {
        booksCustomerId = createContactResp.get("contact").get("contact_id");
    }
}

// 3. تجهيز بنود الفاتورة من المنتجات المرتبطة بالصفقة
lineItemsList = List();
dealProducts = zoho.crm.getRelatedRecords("Products", "Deals", dealId);

for each product in dealProducts
{
    lineItem = Map();
    lineItem.put("name", product.get("Product_Name"));
    lineItem.put("rate", product.get("Unit_Price"));
    lineItem.put("quantity", 1);
    // كود الضريبة 15% للنسخة السعودية
    lineItem.put("tax_id", "YOUR_TAX_ID_15_PERCENT"); 
    lineItemsList.add(lineItem);
}

// في حال عدم وجود بنود مفصلة نعتمد إجمالي الصفقة
if(lineItemsList.size() == 0)
{
    lineItem = Map();
    lineItem.put("name", "خدمات تعاقدية: " + dealDetails.get("Deal_Name"));
    lineItem.put("rate", dealDetails.get("Amount"));
    lineItem.put("quantity", 1);
    lineItem.put("tax_id", "YOUR_TAX_ID_15_PERCENT");
    lineItemsList.add(lineItem);
}

// 4. إنشاء الفاتورة في Zoho Books
invoiceMap = Map();
invoiceMap.put("customer_id", booksCustomerId);
invoiceMap.put("date", today);
invoiceMap.put("due_date", today.addDays(15));
invoiceMap.put("reference_number", dealDetails.get("Deal_Name"));
invoiceMap.put("line_items", lineItemsList);
invoiceMap.put("is_inclusive_tax", false);

createInvoiceResp = zoho.books.createRecord("Invoices", "YOUR_BOOKS_ORG_ID", invoiceMap);

if(createInvoiceResp.get("code") == 0)
{
    invoiceId = createInvoiceResp.get("invoice").get("invoice_id");
    invoiceNumber = createInvoiceResp.get("invoice").get("invoice_number");
    
    // تحديث رقم الفاتورة في سجل الصفقة بـ CRM
    updateDealMap = Map();
    updateDealMap.put("Invoice_Number", invoiceNumber);
    updateDealMap.put("Books_Invoice_ID", invoiceId);
    zoho.crm.updateRecord("Deals", dealId, updateDealMap);
    
    info "تم إنشاء الفاتورة بنجاح برقم: " + invoiceNumber;
}
else
{
    info "خطأ في إنشاء الفاتورة: " + createInvoiceResp.get("message");
}
```

---

## 2. سكريبت التحقق التلقائي من الرقم الضريبي السعودي (15 رقماً)

```deluge
// يتم تفعيلها كـ Validation Rule أو Custom Function قبل حفظ الحساب
// المدخلات: vatNumber (String)

if(vatNumber != null && vatNumber != "")
{
    vatStr = vatNumber.trim();
    // التحقق من الطول وأنه يبدأ بـ 3 وينتهي بـ 3
    if(vatStr.length() != 15 || !vatStr.startsWith("3") || !vatStr.endsWith("3"))
    {
        return "تنبيه: الرقم الضريبي للمملكة العربية السعودية يجب أن يتكون من 15 رقماً، ويبدأ بـ 3 وينتهي بـ 3.";
    }
}
return "";
```

---

## 3. سكريبت Webhook لتحديث حالة الدفع من Zoho Books إلى Zoho CRM

```deluge
// عند سداد الفاتورة في Zoho Books يتم إرسال Webhook لـ CRM
// المدخلات: invoice_id, payment_status, deal_id

if(payment_status == "paid" && deal_id != null)
{
    updateDeal = Map();
    updateDeal.put("Payment_Status", "Paid In Full");
    updateDeal.put("Stage", "Payment Received & Completed");
    zoho.crm.updateRecord("Deals", deal_id.toLong(), updateDeal);
    
    // إرسال إشعار للمسؤول في المنشأة
    sendmail
    [
        from: zoho.adminuserid
        to: "finance@alawadi-cloud.com"
        subject: "إشعار سداد فاتورة جديدة"
        message: "تم سداد الفاتورة رقم " + invoice_number + " بالكامل وتحديث حالة الصفقة في Zoho CRM."
    ]
}
```
