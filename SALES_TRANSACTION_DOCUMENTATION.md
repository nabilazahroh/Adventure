# Dokumentasi Fitur Transaksi Penjualan (Barang Keluar)

## 1. Struktur Database

### Tabel: `sales`

```sql
CREATE TABLE sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    selling_price DECIMAL(15,2) NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    profit DECIMAL(15,2) NULL,
    notes TEXT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Penjelasan Kolom:**
- `transaction_date`: Tanggal transaksi penjualan
- `product_id`: ID produk yang dijual (relasi ke tabel products)
- `quantity`: Jumlah produk yang dijual
- `selling_price`: Harga jual per unit
- `total`: Total harga (quantity × selling_price)
- `profit`: Keuntungan (quantity × (selling_price - purchase_price))
- `notes`: Catatan tambahan (opsional)
- `user_id`: ID user yang melakukan transaksi

### File Migration
Location: `/database/migrations/2025_12_02_053505_create_sales_table.php`

---

## 2. Model Eloquent

### Sale Model
Location: `/app/Models/Sale.php`

```php
protected $fillable = [
    'transaction_date',
    'product_id',
    'quantity',
    'selling_price',
    'total',
    'profit',
    'notes',
    'user_id',
];

protected $casts = [
    'transaction_date' => 'date',
    'quantity' => 'integer',
    'selling_price' => 'decimal:2',
    'total' => 'decimal:2',
    'profit' => 'decimal:2',
];
```

**Relasi:**
- `product()`: BelongsTo Product
- `user()`: BelongsTo User

---

## 3. Backend Logic (Controller)

### SaleController
Location: `/app/Http/Controllers/SaleController.php`

#### Method: `store()` - Menyimpan Transaksi Penjualan

**Validasi Input:**
```php
$validated = $request->validate([
    'transaction_date' => ['required', 'date'],
    'product_id' => ['required', 'exists:products,id'],
    'quantity' => ['required', 'integer', 'min:1'],
    'selling_price' => ['nullable', 'numeric', 'min:0'],
    'notes' => ['nullable', 'string', 'max:1000'],
]);
```

**Flow Proses:**

1. **Validasi Input**
   - Tanggal transaksi harus valid
   - Product ID harus ada di database
   - Quantity minimal 1
   - Selling price opsional (jika kosong, gunakan harga default produk)

2. **Database Transaction (BEGIN)**
   ```php
   DB::beginTransaction();
   ```

3. **Validasi Stok**
   ```php
   $product = Product::findOrFail($validated['product_id']);
   
   if ($product->stock < $validated['quantity']) {
       return back()
           ->withInput()
           ->withErrors(['quantity' => "Stok tidak mencukupi. Stok tersedia: {$product->stock}"]);
   }
   ```

4. **Kalkulasi Harga & Profit**
   ```php
   $sellingPrice = $validated['selling_price'] ?? $product->selling_price;
   $total = $validated['quantity'] * $sellingPrice;
   $profit = $validated['quantity'] * ($sellingPrice - $product->purchase_price);
   ```

5. **Simpan Transaksi**
   ```php
   Sale::create([
       'transaction_date' => $validated['transaction_date'],
       'product_id' => $validated['product_id'],
       'quantity' => $validated['quantity'],
       'selling_price' => $sellingPrice,
       'total' => $total,
       'profit' => $profit,
       'notes' => $validated['notes'],
       'user_id' => Auth::id(),
   ]);
   ```

6. **Update Stok Produk (Pengurangan Otomatis)**
   ```php
   $product->decrement('stock', $validated['quantity']);
   ```

7. **Commit Transaction**
   ```php
   DB::commit();
   ```

8. **Error Handling**
   ```php
   catch (\Exception $e) {
       DB::rollBack();
       return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan...']);
   }
   ```

---

## 4. Validasi Transaksi

### Validasi Utama:

1. **Validasi Stok Tersedia**
   - Sistem mengecek stok produk sebelum menyimpan transaksi
   - Jika stok < quantity yang diminta → transaksi ditolak
   - Error message: "Stok tidak mencukupi. Stok tersedia: X"

2. **Validasi Data Input**
   - `transaction_date`: Required, harus format date
   - `product_id`: Required, harus ada di tabel products
   - `quantity`: Required, integer, minimal 1
   - `selling_price`: Optional, numeric, minimal 0
   - `notes`: Optional, string, max 1000 karakter

3. **Atomic Transaction**
   - Menggunakan database transaction (BEGIN/COMMIT/ROLLBACK)
   - Jika ada error di tengah proses, semua perubahan dibatalkan
   - Memastikan data consistency

---

## 5. Query Pengurangan Stok

### Pseudocode:
```
BEGIN TRANSACTION

1. SELECT product FROM products WHERE id = product_id
2. IF product.stock < quantity THEN
     ROLLBACK
     RETURN error "Stok tidak mencukupi"
   END IF

3. INSERT INTO sales (transaction_date, product_id, quantity, ...)
   VALUES (...)

4. UPDATE products 
   SET stock = stock - quantity
   WHERE id = product_id

5. COMMIT TRANSACTION
```

### SQL Query Aktual:
```sql
-- Cek stok
SELECT * FROM products WHERE id = ? FOR UPDATE;

-- Insert transaksi
INSERT INTO sales (transaction_date, product_id, quantity, selling_price, total, profit, notes, user_id, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW());

-- Update stok (pengurangan otomatis)
UPDATE products 
SET stock = stock - ?, 
    updated_at = NOW()
WHERE id = ?;
```

---

## 6. Dashboard Updates

### DashboardController
Location: `/app/Http/Controllers/DashboardController.php`

#### Metrics yang Ditampilkan:

1. **Total Penjualan Hari Ini**
   ```php
   $todaySales = Sale::whereDate('transaction_date', Carbon::today())->sum('total');
   ```

2. **Produk Terlaris**
   ```php
   $topProducts = Product::select('products.*')
       ->selectRaw('COALESCE(SUM(sales.quantity), 0) as total_sold')
       ->leftJoin('sales', 'products.id', '=', 'sales.product_id')
       ->groupBy('products.id')
       ->orderByDesc('total_sold')
       ->limit(5)
       ->get();
   ```

3. **Notifikasi Stok Hampir Habis (≤ 5)**
   ```php
   $lowStockCount = Product::where('stock', '<=', 5)->count();
   
   $lowStockProducts = Product::where('stock', '<=', 5)
       ->orderBy('stock', 'asc')
       ->get();
   ```

4. **Aktivitas Terbaru (Recent Sales)**
   ```php
   $recentActivities = Sale::with('product', 'user')
       ->orderBy('created_at', 'desc')
       ->limit(5)
       ->get();
   ```

---

## 7. Routes

Location: `/routes/web.php`

```php
Route::middleware('auth')->group(function () {
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
});
```

**Available Routes:**
- `GET /sales` → sales.index (Daftar transaksi)
- `GET /sales/create` → sales.create (Form tambah transaksi)
- `POST /sales` → sales.store (Simpan transaksi)
- `GET /sales/{sale}` → sales.show (Detail transaksi)

---

## 8. Views

### Form Transaksi Penjualan
Location: `/resources/views/sales/create.blade.php`

**Fitur Form:**
- Tanggal transaksi (default: hari ini)
- Dropdown pilih produk (hanya produk dengan stok > 0)
- Auto-display stok tersedia saat produk dipilih
- Input jumlah keluar (dengan validasi max = stok tersedia)
- Input harga jual (opsional, default dari produk)
- Textarea catatan (opsional)

**JavaScript Features:**
```javascript
// Auto-update stok tersedia saat produk dipilih
productSelect.addEventListener('change', function() {
    const stock = selectedOption.getAttribute('data-stock');
    availableStock.textContent = stock;
    quantityInput.max = stock;
});
```

### Daftar Transaksi
Location: `/resources/views/sales/index.blade.php`

Menampilkan:
- Tanggal transaksi
- Nama produk & satuan
- Jumlah keluar
- Harga jual
- Total
- Keuntungan
- Catatan

---

## 9. Testing Checklist

### Skenario Testing:

1. ✅ **Transaksi Normal**
   - Input semua field dengan benar
   - Stok mencukupi
   - Expected: Transaksi berhasil, stok berkurang

2. ✅ **Validasi Stok Tidak Cukup**
   - Quantity > stok tersedia
   - Expected: Error "Stok tidak mencukupi"

3. ✅ **Harga Jual Opsional**
   - Kosongkan field harga jual
   - Expected: Menggunakan harga default produk

4. ✅ **Dashboard Update**
   - Setelah transaksi
   - Expected: Total penjualan hari ini bertambah

5. ✅ **Low Stock Alert**
   - Produk dengan stok ≤ 5
   - Expected: Muncul notifikasi di dashboard

---

## 10. Cara Menjalankan Migration

```bash
# Fresh migration (hapus semua data)
php artisan migrate:fresh --seed

# Atau migration biasa
php artisan migrate
```

---

## 11. API Endpoints (Jika Diperlukan)

Jika ingin membuat API untuk mobile/external:

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/products/available', [ProductController::class, 'available']);
});
```

---

## 12. Security Considerations

1. **Authentication Required**: Semua route sales memerlukan login
2. **CSRF Protection**: Form menggunakan @csrf token
3. **SQL Injection Prevention**: Menggunakan Eloquent ORM & prepared statements
4. **Input Validation**: Semua input divalidasi sebelum disimpan
5. **Database Transaction**: Memastikan data consistency
6. **Authorization**: User ID otomatis dari Auth::id()

---

## 13. Future Enhancements

Fitur yang bisa ditambahkan:
- Export transaksi ke Excel/PDF
- Filter transaksi by date range
- Laporan penjualan bulanan
- Grafik penjualan
- Barcode scanning untuk produk
- Print receipt/invoice
- Return/refund transaction
- Batch transaction (multiple products)

---

## Kontak & Support

Untuk pertanyaan atau issue, silakan hubungi tim development.

**Last Updated**: December 18, 2025
