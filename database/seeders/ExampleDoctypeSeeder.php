<?php

namespace Doctypes\Database\Seeders;

use Illuminate\Database\Seeder;
use Doctypes\Models\Doctype;
use Doctypes\Models\DoctypeField;

class ExampleDoctypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Contoh seeder untuk membuat doctype Customer dan Product
     * 
     * Cara run:
     * php artisan db:seed --class="NgodingSkuyy\Doctypes\Database\Seeders\ExampleDoctypeSeeder"
     */
    public function run(): void
    {
        $this->createCustomerDoctype();
        $this->createProductDoctype();

        $this->command->info('✓ Example doctypes created successfully!');
        $this->command->info('✓ Next steps:');
        $this->command->info('  1. php artisan doctype:generate Customer');
        $this->command->info('  2. php artisan doctype:generate Product');
        $this->command->info('  3. php artisan migrate');
    }

    /**
     * Create Customer doctype dengan field-field umum
     */
    private function createCustomerDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'Customer',
            'label' => 'Customer Management',
            'description' => 'Manage customer data and information',
            'is_active' => true,
        ]);

        $fields = [
            [
                'fieldname' => 'full_name',
                'label' => 'Full Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 1,
                'description' => 'Customer full name',
            ],
            [
                'fieldname' => 'email',
                'label' => 'Email Address',
                'fieldtype' => 'email',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'sort_order' => 2,
                'description' => 'Customer email for communication',
            ],
            [
                'fieldname' => 'phone',
                'label' => 'Phone Number',
                'fieldtype' => 'text',
                'required' => false,
                'in_list_view' => true,
                'sort_order' => 3,
                'description' => 'Customer phone number',
            ],
            [
                'fieldname' => 'address',
                'label' => 'Address',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 4,
                'description' => 'Customer full address',
            ],
            [
                'fieldname' => 'customer_type',
                'label' => 'Customer Type',
                'fieldtype' => 'select',
                'options' => 'Individual,Corporate,Partner,VIP',
                'required' => true,
                'in_standard_filter' => true,
                'in_list_view' => true,
                'sort_order' => 5,
                'description' => 'Type of customer',
            ],
            [
                'fieldname' => 'is_active',
                'label' => 'Active Status',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'in_list_view' => true,
                'sort_order' => 6,
                'description' => 'Whether customer is active',
            ],
            [
                'fieldname' => 'join_date',
                'label' => 'Join Date',
                'fieldtype' => 'date',
                'required' => true,
                'sort_order' => 7,
                'description' => 'Date when customer joined',
            ],
            [
                'fieldname' => 'notes',
                'label' => 'Internal Notes',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Internal notes about customer',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ Customer doctype created with {$doctype->fields()->count()} fields");
    }

    /**
     * Create Product doctype untuk catalog
     */
    private function createProductDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'Product',
            'label' => 'Product Catalog',
            'description' => 'Manage product inventory and catalog',
            'is_active' => true,
        ]);

        $fields = [
            [
                'fieldname' => 'sku',
                'label' => 'SKU',
                'fieldtype' => 'text',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'sort_order' => 1,
                'description' => 'Product SKU (Stock Keeping Unit)',
            ],
            [
                'fieldname' => 'name',
                'label' => 'Product Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 2,
                'description' => 'Product display name',
            ],
            [
                'fieldname' => 'description',
                'label' => 'Description',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 3,
                'description' => 'Product description for customers',
            ],
            [
                'fieldname' => 'price',
                'label' => 'Price',
                'fieldtype' => 'number',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 4,
                'description' => 'Product selling price',
            ],
            [
                'fieldname' => 'cost',
                'label' => 'Cost',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 5,
                'description' => 'Product cost price',
            ],
            [
                'fieldname' => 'category',
                'label' => 'Category',
                'fieldtype' => 'select',
                'options' => 'Electronics,Clothing,Books,Home & Garden,Sports,Automotive,Health & Beauty',
                'required' => true,
                'in_standard_filter' => true,
                'in_list_view' => true,
                'sort_order' => 6,
                'description' => 'Product category',
            ],
            [
                'fieldname' => 'brand',
                'label' => 'Brand',
                'fieldtype' => 'text',
                'required' => false,
                'in_list_view' => true,
                'sort_order' => 7,
                'description' => 'Product brand',
            ],
            [
                'fieldname' => 'weight',
                'label' => 'Weight (kg)',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Product weight in kilograms',
            ],
            [
                'fieldname' => 'specifications',
                'label' => 'Specifications',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 9,
                'description' => 'Product technical specifications (JSON format)',
            ],
            [
                'fieldname' => 'images',
                'label' => 'Images',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 10,
                'description' => 'Product images URLs (JSON array)',
            ],
            [
                'fieldname' => 'stock_quantity',
                'label' => 'Stock Quantity',
                'fieldtype' => 'number',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 11,
                'description' => 'Available stock quantity',
            ],
            [
                'fieldname' => 'is_featured',
                'label' => 'Featured Product',
                'fieldtype' => 'checkbox',
                'default_value' => 0,
                'in_list_view' => true,
                'sort_order' => 12,
                'description' => 'Mark as featured product',
            ],
            [
                'fieldname' => 'is_active',
                'label' => 'Active Status',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'in_list_view' => true,
                'sort_order' => 13,
                'description' => 'Product active status',
            ],
            [
                'fieldname' => 'launch_date',
                'label' => 'Launch Date',
                'fieldtype' => 'date',
                'required' => false,
                'sort_order' => 14,
                'description' => 'Product launch date',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ Product doctype created with {$doctype->fields()->count()} fields");
    }
}
