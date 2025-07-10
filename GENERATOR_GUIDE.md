# Code Generator Guide

Setelah membuat DocType, Anda bisa menggunakan generator untuk otomatis membuat file Laravel seperti Model, Controller, Request, Resource, dan Migration.

## Cara Menggunakan Generator

### 1. Buat DocType Terlebih Dahulu

Pertama, buat DocType untuk User (atau entity lainnya):

```php
use Doctypes\Models\Doctype;
use Doctypes\Models\DoctypeField;

$userDoctype = Doctype::create([
    'name' => 'User',
    'label' => 'User Management',
    'description' => 'User management system',
    'icon' => 'user',
    'color' => '#3b82f6'
]);

// Tambahkan fields
$fields = [
    [
        'fieldname' => 'name',
        'label' => 'Full Name',
        'fieldtype' => 'text',
        'required' => true,
        'in_list_view' => true,
        'in_standard_filter' => true
    ],
    [
        'fieldname' => 'email',
        'label' => 'Email Address',
        'fieldtype' => 'email',
        'required' => true,
        'unique' => true,
        'in_list_view' => true
    ],
    [
        'fieldname' => 'phone',
        'label' => 'Phone Number',
        'fieldtype' => 'text',
        'required' => false,
        'in_list_view' => true
    ],
    [
        'fieldname' => 'is_active',
        'label' => 'Active Status',
        'fieldtype' => 'checkbox',
        'required' => false,
        'default_value' => true,
        'in_list_view' => true,
        'in_standard_filter' => true
    ],
    [
        'fieldname' => 'birth_date',
        'label' => 'Birth Date',
        'fieldtype' => 'date',
        'required' => false
    ]
];

foreach ($fields as $field) {
    DoctypeField::create(array_merge($field, ['doctype_id' => $userDoctype->id]));
}
```

### 2. Generate Files

Sekarang Anda bisa generate berbagai file:

#### Generate Semua File Sekaligus

```bash
php artisan doctype:generate User --all
```

#### Generate File Tertentu

```bash
# Generate hanya controller
php artisan doctype:generate User --controller

# Generate model dan request
php artisan doctype:generate User --model --request

# Generate dengan force overwrite
php artisan doctype:generate User --controller --force
```

#### Dry Run (Preview)

```bash
# Lihat apa yang akan di-generate tanpa membuat file
php artisan doctype:generate User --all --dry-run
```

### 3. Hasil Generate

Generator akan membuat file-file berikut:

#### Model (`app/Models/User.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_active',
        'birth_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date'
    ];

    public function scopeByName($query, $value)
    {
        return $query->where('name', $value);
    }

    public function scopeByIsActive($query, $value)
    {
        return $query->where('is_active', $value);
    }
}
```

#### Controller (`app/Http/Controllers/UserController.php`)
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->has('name')) {
            $query->where('name', $request->get('name'));
        }

        if ($request->has('isActive')) {
            $query->where('is_active', $request->get('isActive'));
        }

        $users = $query->paginate(15);

        return UserResource::collection($users);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
```

#### Request (`app/Http/Requests/UserRequest.php`)
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'birth_date' => 'nullable|date'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The Full Name field is required.',
            'email.required' => 'The Email Address field is required.',
            'email.unique' => 'The Email Address has already been taken.'
        ];
    }
}
```

#### Resource (`app/Http/Resources/UserResource.php`)
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
            'birth_date' => $this->birth_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
```

#### Migration (`database/migrations/2024_01_01_000000_create_users_table.php`)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

### 4. Setelah Generate

#### Jalankan Migration
```bash
php artisan migrate
```

#### Tambahkan Routes
Tambahkan ke `routes/api.php`:
```php
Route::apiResource('users', UserController::class);
```

#### Test API
```bash
# GET semua users
curl http://localhost:8000/api/users

# POST user baru
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","phone":"123456789"}'

# GET user dengan filter
curl http://localhost:8000/api/users?name=John&isActive=1
```

## Command Options

```bash
php artisan doctype:generate {doctype}
    {--model : Generate model file}
    {--controller : Generate controller file}
    {--request : Generate request file}
    {--resource : Generate resource file}
    {--migration : Generate migration file}
    {--all : Generate all files}
    {--force : Overwrite existing files}
    {--dry-run : Show what would be generated without creating files}
```

## Examples

```bash
# Generate everything untuk Customer
php artisan doctype:generate Customer --all

# Generate hanya model dan controller untuk Product
php artisan doctype:generate Product --model --controller

# Preview apa yang akan di-generate untuk Order
php artisan doctype:generate Order --all --dry-run

# Force overwrite existing UserController
php artisan doctype:generate User --controller --force
```

## Tips

1. **Gunakan dry-run** terlebih dahulu untuk melihat apa yang akan di-generate
2. **Backup file existing** sebelum menggunakan `--force`
3. **Customize hasil generate** sesuai kebutuhan aplikasi Anda
4. **Test migration** di environment development terlebih dahulu

Dengan generator ini, Anda bisa dengan cepat membuat CRUD lengkap untuk entity apapun hanya dengan mendefinisikan DocType! 🚀
