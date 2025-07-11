# Quick Start Tutorial - Doctype Generator

## 📋 Step-by-Step Tutorial

### 1. Installation (Jika Belum)
```bash
composer require ngodingskuyy/doctypes
php artisan vendor:publish --provider="NgodingSkuyy\Doctypes\Providers\DoctypeServiceProvider"
php artisan migrate
```

### 2. Lihat Demo & Examples
```bash
# Lihat demo dan contoh command
php artisan doctype:demo

# Buat sample doctypes (Customer & Product)
php artisan db:seed --class="NgodingSkuyy\Doctypes\Database\Seeders\ExampleDoctypeSeeder"
```

### 3. Generate Files
```bash
# Generate semua file untuk Customer
php artisan doctype:generate Customer

# Generate semua file untuk Product  
php artisan doctype:generate Product

# Atau generate demo files sekaligus
php artisan doctype:demo --generate
```

### 4. Run Migration
```bash
php artisan migrate
```

### 5. Setup Routes
Tambah ke `routes/api.php`:
```php
Route::apiResource('customers', CustomerController::class);
Route::apiResource('products', ProductController::class);
```

### 6. Test API (Optional)
```bash
# Test Customer API
curl -X GET http://your-app.test/api/customers
curl -X POST http://your-app.test/api/customers \
  -H "Content-Type: application/json" \
  -d '{"full_name":"John Doe","email":"john@example.com","customer_type":"Individual","join_date":"2024-01-01"}'

# Test Product API  
curl -X GET http://your-app.test/api/products
curl -X POST http://your-app.test/api/products \
  -H "Content-Type: application/json" \
  -d '{"sku":"PROD001","name":"Sample Product","price":99.99,"category":"Electronics","stock_quantity":10}'
```

### 7. Frontend Integration
```vue
<template>
  <div>
    <!-- List existing customers -->
    <DoctypeList />
    
    <!-- Create new customer with dynamic form -->
    <GeneratedForm 
      doctype-name="Customer" 
      mode="create"
      @save="handleSave" 
    />
  </div>
</template>

<script setup>
import { DoctypeList, GeneratedForm } from '@/features/doctypes';

const handleSave = async (formData) => {
  await fetch('/api/customers', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
  });
};
</script>
```

## 🔧 Advanced Usage

### Custom Doctype via Code
```php
// Create custom doctype programmatically
$doctype = Doctype::create([
    'name' => 'Invoice',
    'label' => 'Invoice Management',
    'description' => 'Manage customer invoices',
]);

// Add fields
$fields = [
    ['fieldname' => 'invoice_number', 'label' => 'Invoice #', 'fieldtype' => 'text', 'required' => true],
    ['fieldname' => 'customer_name', 'label' => 'Customer', 'fieldtype' => 'text', 'required' => true],
    ['fieldname' => 'total_amount', 'label' => 'Total', 'fieldtype' => 'number', 'required' => true],
    ['fieldname' => 'due_date', 'label' => 'Due Date', 'fieldtype' => 'date', 'required' => true],
    ['fieldname' => 'status', 'label' => 'Status', 'fieldtype' => 'select', 'options' => 'Draft,Sent,Paid,Overdue'],
];

foreach ($fields as $field) {
    DoctypeField::create(['doctype_id' => $doctype->id, ...$field]);
}

// Generate files
php artisan doctype:generate Invoice
```

### Module-based Generation
```bash
# Generate ke specific module
php artisan doctype:generate Customer --module=CRM
php artisan doctype:generate Product --module=Inventory
php artisan doctype:generate Invoice --module=Accounting

# Files akan generate ke:
# app/Modules/CRM/Models/Customer.php
# app/Modules/CRM/Http/Controllers/CustomerController.php
# dst...
```

### Selective File Generation
```bash
# Hanya generate model dan migration
php artisan doctype:generate Customer --types=model,migration

# Hanya generate controller dan request
php artisan doctype:generate Customer --types=controller,request

# Available types: model, controller, request, resource, migration
```

## 🎯 Common Use Cases

### 1. E-commerce Store
```bash
# Create product catalog
php artisan db:seed --class="NgodingSkuyy\Doctypes\Database\Seeders\ExampleDoctypeSeeder"
php artisan doctype:generate Product
php artisan migrate
```

### 2. CRM System  
```bash
# Customer management
php artisan doctype:generate Customer --module=CRM
# Lead tracking (create Lead doctype first)
php artisan doctype:generate Lead --module=CRM
```

### 3. Content Management
```bash
# Blog posts (create BlogPost doctype first)
php artisan doctype:generate BlogPost --module=Content
# Page management (create Page doctype first)  
php artisan doctype:generate Page --module=Content
```

### 4. HR Management
```bash
# Employee records (create Employee doctype first)
php artisan doctype:generate Employee --module=HR
# Leave applications (create LeaveApplication doctype first)
php artisan doctype:generate LeaveApplication --module=HR
```

## 🚀 Tips & Best Practices

### 1. Field Naming
- Use snake_case: `full_name`, `email_address`, `phone_number`
- Be descriptive: `customer_type` not just `type`
- Avoid reserved words: use `is_active` not `active`

### 2. Field Types Usage
- `text`: Short strings (names, codes, etc.)
- `textarea`: Long text (descriptions, notes)
- `email`: Email validation built-in
- `number`: Numeric values (prices, quantities)
- `select`: Predefined options (status, categories)
- `checkbox`: Boolean values (active/inactive, featured)
- `date`: Date picker (join_date, due_date)
- `json`: Complex data (specifications, metadata)

### 3. Performance Tips
- Set `in_list_view = true` for important fields only
- Use `in_standard_filter = true` for searchable fields
- Set proper `sort_order` for logical field arrangement
- Use `unique = true` for fields that need uniqueness (SKU, email)

### 4. Frontend Integration
- Use `GeneratedForm` for consistent dynamic forms
- Leverage `useDoctypes` composable for API calls
- Implement proper error handling
- Add loading states for better UX

## 🔄 Reset Demo (Cleanup)
```bash
# Remove demo data and files
php artisan doctype:demo --reset

# Fresh start
php artisan migrate:fresh
php artisan db:seed --class="NgodingSkuyy\Doctypes\Database\Seeders\DoctypeSeeder"
```

Selesai! Dengan tutorial ini lo bisa langsung pakai doctype generator untuk project apapun. 🎉
