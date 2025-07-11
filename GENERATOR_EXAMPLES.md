# Laravel Doctype Generator - Contoh Penggunaan

## 1. Instalasi Package

```bash
composer require ngodingskuyy/doctypes
php artisan vendor:publish --provider="NgodingSkuyy\Doctypes\Providers\DoctypeServiceProvider"
php artisan migrate
```

## 2. Membuat Doctype Baru

### Via Web Interface (Recommended)

1. Buka `/doctypes` di browser
2. Klik "Create New Doctype"
3. Isi form dengan field-field yang diinginkan
4. Save doctype

### Via Seeder (Programmatic)

```php
<?php
// database/seeders/CustomDoctypeSeeder.php

use Illuminate\Database\Seeder;
use Doctypes\Models\Doctype;
use Doctypes\Models\DoctypeField;

class CustomDoctypeSeeder extends Seeder
{
    public function run()
    {
        // Buat Doctype untuk Customer
        $customerDoctype = Doctype::create([
            'name' => 'Customer',
            'label' => 'Customer Management',
            'description' => 'Manage customer data',
            'is_active' => true,
        ]);

        // Tambah field-field
        $fields = [
            [
                'fieldname' => 'full_name',
                'label' => 'Full Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 1,
            ],
            [
                'fieldname' => 'email',
                'label' => 'Email Address',
                'fieldtype' => 'email',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'sort_order' => 2,
            ],
            [
                'fieldname' => 'phone',
                'label' => 'Phone Number',
                'fieldtype' => 'text',
                'required' => false,
                'in_list_view' => true,
                'sort_order' => 3,
            ],
            [
                'fieldname' => 'address',
                'label' => 'Address',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 4,
            ],
            [
                'fieldname' => 'customer_type',
                'label' => 'Customer Type',
                'fieldtype' => 'select',
                'options' => 'Individual,Corporate,Partner',
                'required' => true,
                'in_standard_filter' => true,
                'sort_order' => 5,
            ],
            [
                'fieldname' => 'is_active',
                'label' => 'Active',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'in_list_view' => true,
                'sort_order' => 6,
            ],
            [
                'fieldname' => 'join_date',
                'label' => 'Join Date',
                'fieldtype' => 'date',
                'required' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($fields as $index => $fieldData) {
            DoctypeField::create([
                'doctype_id' => $customerDoctype->id,
                ...$fieldData
            ]);
        }
    }
}
```

## 3. Generate Files via Artisan Command

### Basic Generation

```bash
# Generate semua file untuk Customer doctype
php artisan doctype:generate Customer

# Output:
# ✓ Model created: app/Models/Customer.php
# ✓ Controller created: app/Http/Controllers/CustomerController.php
# ✓ Request created: app/Http/Requests/CustomerRequest.php
# ✓ Resource created: app/Http/Resources/CustomerResource.php
# ✓ Migration created: database/migrations/xxxx_create_customers_table.php
```

### Generate Specific Files Only

```bash
# Hanya generate model dan controller
php artisan doctype:generate Customer --types=model,controller

# Hanya generate migration
php artisan doctype:generate Customer --types=migration

# Generate dengan force (overwrite existing files)
php artisan doctype:generate Customer --force
```

### Custom Module/Directory

```bash
# Generate ke module tertentu
php artisan doctype:generate Customer --module=CRM

# Output akan ke:
# - app/Modules/CRM/Models/Customer.php
# - app/Modules/CRM/Http/Controllers/CustomerController.php
# - dst...
```

## 4. Hasil Generated Files

### Model (app/Models/Customer.php)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address',
        'customer_type',
        'is_active',
        'join_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'join_date' => 'date',
    ];
}
```

### Controller (app/Http/Controllers/CustomerController.php)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where('full_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->customer_type, function ($query, $type) {
                $query->where('customer_type', $type);
            })
            ->paginate($request->per_page ?? 15);

        return CustomerResource::collection($customers);
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        return new CustomerResource($customer);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
```

### Migration (database/migrations/xxxx_create_customers_table.php)

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->enum('customer_type', ['Individual', 'Corporate', 'Partner']);
            $table->boolean('is_active')->default(true);
            $table->date('join_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
```

## 5. Menggunakan di Frontend

### Get Form Schema

```javascript
// Di Vue component
import { useDoctypes } from '@/features/doctypes';

const { getFormSchema } = useDoctypes();

// Ambil schema untuk generate form
const schema = await getFormSchema('Customer');

// schema berisi:
// {
//   doctype: 'Customer',
//   title: 'Customer Management',
//   description: 'Manage customer data',
//   fields: [
//     {
//       name: 'full_name',
//       label: 'Full Name',
//       type: 'text',
//       required: true
//     },
//     // ... field lainnya
//   ]
// }
```

### Dynamic Form Component

```vue
<template>
  <GeneratedForm
    doctype-name="Customer"
    mode="create"
    @save="handleSave"
  />
</template>

<script setup>
import { GeneratedForm } from '@/features/doctypes';

const handleSave = async (formData) => {
  // formData contains all field values
  console.log('Saving customer:', formData);
  
  // Send to your API
  await fetch('/api/customers', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
  });
};
</script>
```

## 6. API Endpoints (Otomatis Tersedia)

```bash
# Doctype Management
GET    /api/doctypes/doctypes          # List all doctypes
POST   /api/doctypes/doctypes          # Create new doctype
GET    /api/doctypes/doctypes/{id}     # Get specific doctype
PUT    /api/doctypes/doctypes/{id}     # Update doctype
DELETE /api/doctypes/doctypes/{id}     # Delete doctype

# Form Schema
GET    /api/doctypes/doctypes/{name}/schema  # Get form schema

# File Generation
POST   /api/doctypes/doctypes/generate       # Generate files via API
```

## 7. Complete Example Workflow

### Step 1: Create Doctype
```bash
php artisan db:seed --class=CustomDoctypeSeeder
```

### Step 2: Generate Files
```bash
php artisan doctype:generate Customer
```

### Step 3: Run Migration
```bash
php artisan migrate
```

### Step 4: Add Routes (routes/api.php)
```php
Route::apiResource('customers', CustomerController::class);
```

### Step 5: Use in Frontend
```vue
<template>
  <div>
    <DoctypeList doctype-name="Customer" />
    <GeneratedForm doctype-name="Customer" @save="handleSave" />
  </div>
</template>
```

## 8. Advanced Examples

### Complex Doctype: Product

```php
// Seeder untuk Product doctype
$productDoctype = Doctype::create([
    'name' => 'Product',
    'label' => 'Product Catalog',
    'description' => 'Manage product inventory',
]);

$fields = [
    [
        'fieldname' => 'sku',
        'label' => 'SKU',
        'fieldtype' => 'text',
        'required' => true,
        'unique' => true,
        'in_list_view' => true,
    ],
    [
        'fieldname' => 'name',
        'label' => 'Product Name',
        'fieldtype' => 'text',
        'required' => true,
        'in_list_view' => true,
    ],
    [
        'fieldname' => 'description',
        'label' => 'Description',
        'fieldtype' => 'textarea',
    ],
    [
        'fieldname' => 'price',
        'label' => 'Price',
        'fieldtype' => 'number',
        'required' => true,
        'in_list_view' => true,
    ],
    [
        'fieldname' => 'category',
        'label' => 'Category',
        'fieldtype' => 'select',
        'options' => 'Electronics,Clothing,Books,Home,Sports',
        'in_standard_filter' => true,
    ],
    [
        'fieldname' => 'specifications',
        'label' => 'Specifications',
        'fieldtype' => 'json',
    ],
    [
        'fieldname' => 'images',
        'label' => 'Product Images',
        'fieldtype' => 'json',
    ],
    [
        'fieldname' => 'is_featured',
        'label' => 'Featured Product',
        'fieldtype' => 'checkbox',
        'default_value' => 0,
    ],
    [
        'fieldname' => 'launch_date',
        'label' => 'Launch Date',
        'fieldtype' => 'date',
    ],
];
```

### Generate dan Setup

```bash
# Generate files
php artisan doctype:generate Product

# Run migration
php artisan migrate

# Add routes
echo "Route::apiResource('products', ProductController::class);" >> routes/api.php
```

Gitu bro! Dengan contoh di atas, lo bisa:

1. **Buat doctype** via seeder atau web interface
2. **Generate semua files** dengan 1 command
3. **Langsung pakai** di frontend dengan dynamic forms
4. **Full CRUD** ready to use

Simple, clean, dan sesuai scope original! 🚀
