# Accounting API Contract

Dokumen ini adalah **kontrak resmi antara Backend dan Frontend**  
untuk modul akuntansi & keuangan.

Semua response di sini adalah **final** dan **bersumber dari journal**.

---

## 1. Prinsip Umum (WAJIB DIPAHAMI)

1. Semua angka keuangan **tidak disimpan**, tapi **dihitung dari journal**
2. Frontend **dilarang menghitung saldo**
3. Frontend **tidak boleh mengubah data keuangan langsung**
4. Backend bertanggung jawab penuh atas:
   - Akuntansi
   - Konsistensi debit = kredit
5. Jika response berubah:
   - File ini **HARUS diupdate lebih dulu**

---

## 2. Dashboard Summary

### GET /api/dashboard/summary

Ringkasan keuangan real-time untuk dashboard utama.

### Response
```json
{
  "cash": {
    "coa_code": "1101",
    "balance": 12500000
  },
  "bank": {
    "coa_code": "1102",
    "balance": 8400000
  },
  "accounts_receivable": {
    "coa_code": "1201",
    "balance": 6200000
  },
  "revenue": {
    "this_month": 18500000
  },
  "expense": {
    "this_month": 4300000
  }
}


## 3. Ledger (Detail per Akun)
GET /api/ledger/{coa_code}

Query Params:

from (YYYY-MM-DD)
##Response
to (YYYY-MM-DD)
{
  "coa": {
    "code": "1101",
    "name": "Kas"
  },
  "opening_balance": 5000000,
  "transactions": [
    {
      "date": "2026-01-03",
      "description": "Penjualan voucher harian",
      "debit": 150000,
      "credit": 0,
      "running_balance": 5150000
    },
    {
      "date": "2026-01-10",
      "description": "Biaya operasional",
      "debit": 0,
      "credit": 250000,
      "running_balance": 4900000
    }
  ],
  "closing_balance": 4900000
}



4. Invoice List (Business View)
GET /api/invoices

Query Params (optional):

status = unpaid | partial | paid

#Response 
{
  "data": [
    {
      "id": 21,
      "customer_name": "John Doe",
      "pppoe": "jd123",
      "package_name": "Paket 20 Mbps",
      "billing_period": "2026-01",
      "total_amount": 350000,
      "paid_amount": 150000,
      "outstanding_amount": 200000,
      "status": "partial"
    }
  ]
}
NOTES:status adalah logic bisnis, TIdak selalu sama dengan saldo AR global

5. Create Payment
POST /api/payments

Mencatat pembayaran customer dan otomatis membuat journal.

Request
{
  "invoice_id": 21,
  "payment_date": "2026-01-15",
  "amount": 200000,
  "method": "cash",
  "note": "Bayar sisa tagihan"
}

Response
{
  "status": "success",
  "payment_id": 88,
  "journal_created": true
}

Notes

Payment = event bisnis

Journal dibuat otomatis oleh backend


6. Trial Balance
GET /api/reports/trial-balance

Query Params:

month (YYYY-MM)

Response
{
  "period": "2026-01",
  "accounts": [
    {
      "coa_code": "1101",
      "name": "Kas",
      "debit": 13500000,
      "credit": 7600000
    },
    {
      "coa_code": "1201",
      "name": "Piutang Usaha",
      "debit": 9800000,
      "credit": 3600000
    }
  ],
  "total_debit": 23300000,
  "total_credit": 23300000
}

Validation

total_debit HARUS sama dengan total_credit

Jika tidak sama → bug akuntansi
7. Profit & Loss (Laba Rugi)
GET /api/reports/profit-loss

Query Params:

month (YYYY-MM)

Response
{
  "period": "2026-01",
  "revenue": {
    "voucher": 7200000,
    "service": 11300000,
    "other": 0,
    "total": 18500000
  },
  "expense": {
    "total": 4300000
  },
  "net_profit": 14200000
}

8. Balance Sheet (Neraca)
GET /api/reports/balance-sheet

Query Params:

as_of (YYYY-MM-DD)

Response
{
  "as_of": "2026-01-31",
  "assets": {
    "cash": 12500000,
    "bank": 8400000,
    "accounts_receivable": 6200000,
    "total": 27100000
  },
  "liabilities": {
    "total": 0
  },
  "equity": {
    "retained_earnings": 27100000
  }
}