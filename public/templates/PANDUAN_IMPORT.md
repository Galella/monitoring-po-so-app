# Panduan Import Data - Monitoring PO-SO App

## 📥 Download Template

Template import tersedia di folder `public/templates/`:

- **CM Data**: [template_cm_data.csv](templates/template_cm_data.csv)
- **COINS Data**: [template_coins_data.csv](templates/template_coins_data.csv)

Atau akses via browser:

- `http://localhost:8000/templates/template_cm_data.csv`
- `http://localhost:8000/templates/template_coins_data.csv`

---

## 📋 Template CM Data

### Kolom yang Diperlukan:

| Kolom            | Deskripsi        | Wajib  | Contoh         |
| ---------------- | ---------------- | ------ | -------------- |
| `ppcw`           | Nomor PPCW       | Tidak  | PPCW001        |
| `container`      | Nomor Container  | **YA** | CONT1234567    |
| `seal`           | Nomor Seal       | Tidak  | SEAL001        |
| `shipper`        | Nama Shipper     | Tidak  | PT Shipper A   |
| `consignee`      | Nama Consignee   | Tidak  | PT Consignee B |
| `status`         | Status Container | Tidak  | LOADED / EMPTY |
| `commodity`      | Jenis Barang     | Tidak  | General Cargo  |
| `size`           | Ukuran Container | Tidak  | 20 / 40        |
| `weight`         | Berat (kg)       | Tidak  | 15000          |
| `keterangan`     | Keterangan       | Tidak  | -              |
| `cm`             | Nomor CM         | **YA** | CM2024001      |
| `atd`            | Tanggal ATD      | Tidak  | 2024-01-15     |
| `no_order_coins` | No Order COINS   | Tidak  | ORD001         |

### Cara Import:

1. Buka menu **Data Import → CM Data**
2. Klik tombol **Import Excel** (hijau)
3. Pilih **Area** yang sesuai
4. Upload file Excel/CSV
5. Klik **Submit**

---

## 📋 Template COINS Data

### Kolom yang Diperlukan:

| Kolom               | Deskripsi             | Wajib  | Contoh        |
| ------------------- | --------------------- | ------ | ------------- |
| `cm`                | Nomor CM              | **YA** | CM2024001     |
| `order`             | Nomor Order           | Tidak  | ORD001        |
| `container`         | Nomor Container       | **YA** | CONT1234567   |
| `seal`              | Nomor Seal            | Tidak  | SEAL001       |
| `20` atau `size_20` | Jumlah Container 20ft | Tidak  | 1             |
| `40` atau `size_40` | Jumlah Container 40ft | Tidak  | 0             |
| `no_po`             | Nomor PO              | Tidak  | PO001         |
| `kereta`            | Nomor Kereta          | Tidak  | KA001         |
| `atd`               | Tanggal ATD           | Tidak  | 2024-01-15    |
| `customer`          | Nama Customer         | Tidak  | PT Customer A |
| `stasiun_asal`      | Stasiun Asal          | Tidak  | Jakarta       |
| `stasiun_tujuan`    | Stasiun Tujuan        | Tidak  | Surabaya      |
| `gudang_asal`       | Gudang Asal           | Tidak  | Gudang A      |
| `gudang_tujuan`     | Gudang Tujuan         | Tidak  | Gudang B      |
| `so`                | Nomor SO              | Tidak  | SO001         |
| `submit_so`         | Tanggal Submit SO     | Tidak  | 2024-01-14    |
| `payment`           | Jenis Payment         | Tidak  | Cash / Credit |
| `nominal`           | Nominal (Rp)          | Tidak  | 1000000       |

### Cara Import:

1. Buka menu **Data Import → COINS Data**
2. Klik tombol **Import Excel** (hijau)
3. Pilih **Wilayah** yang sesuai
4. Upload file Excel/CSV
5. Klik **Submit**

---

## ⚠️ Catatan Penting

1. **Format File**: Pastikan file dalam format `.xlsx` atau `.csv`
2. **Header Row**: Baris pertama HARUS berisi nama kolom
3. **Format Tanggal**: Gunakan format `YYYY-MM-DD` (misal: 2024-01-15)
4. **Kolom Wajib**: `cm` dan `container` harus diisi untuk matching
5. **Nama Kolom**: Gunakan huruf kecil, spasi diganti underscore (`_`)

---

## 🔄 Proses Matching

Setelah import kedua data:

1. Buka menu **Laporan → Data Matching**
2. Lihat hasil matching di 3 tab:
    - ✅ **Matched**: Data CM & COINS yang cocok
    - ⚠️ **CM Only**: Data CM yang tidak ada di COINS
    - ⚠️ **COINS Only**: Data COINS yang tidak ada di CM

3. Klik **Edit** pada data yang tidak cocok untuk memperbaiki
