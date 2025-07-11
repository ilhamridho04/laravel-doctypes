<?php

namespace Doctypes\Database\Seeders;

use Illuminate\Database\Seeder;
use Doctypes\Models\Doctype;
use Doctypes\Models\DoctypeField;

class ComprehensiveDoctypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates comprehensive demo doctypes showcasing all field types and features
     * 
     * Cara run:
     * php artisan db:seed --class="Doctypes\Database\Seeders\ComprehensiveDoctypeSeeder"
     */
    public function run(): void
    {
        $this->createCustomerDoctype();
        $this->createProductDoctype();
        $this->createInvoiceDoctype();
        $this->createBlogPostDoctype();

        $this->command->info('✓ Comprehensive demo doctypes created successfully!');
        $this->command->info('');
        $this->command->info('📋 Available Doctypes:');
        $this->command->info('  • Customer - Customer management with contact info');
        $this->command->info('  • Product - Product catalog with inventory');
        $this->command->info('  • Invoice - Invoice management with line items');
        $this->command->info('  • BlogPost - Blog post management with SEO');
        $this->command->info('');
        $this->command->info('🔧 Next steps:');
        $this->command->info('  1. php artisan doctype:demo --generate');
        $this->command->info('  2. php artisan migrate');
        $this->command->info('  3. Add routes and test in frontend');
    }

    /**
     * Customer DocType - Complete CRM fields
     */
    private function createCustomerDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'Customer',
            'label' => 'Customer Management',
            'description' => 'Complete customer relationship management system',
            'is_active' => true,
            'icon' => 'users',
            'color' => '#3B82F6',
            'settings' => [
                'enable_workflow' => true,
                'track_changes' => true,
                'allow_duplicate' => false,
            ]
        ]);

        $fields = [
            // Basic Information
            [
                'fieldname' => 'customer_code',
                'label' => 'Customer Code',
                'fieldtype' => 'text',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 1,
                'description' => 'Unique customer identification code',
            ],
            [
                'fieldname' => 'full_name',
                'label' => 'Full Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 2,
                'description' => 'Customer full legal name',
            ],
            [
                'fieldname' => 'email',
                'label' => 'Email Address',
                'fieldtype' => 'email',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'sort_order' => 3,
                'description' => 'Primary email for communication',
            ],
            [
                'fieldname' => 'phone',
                'label' => 'Phone Number',
                'fieldtype' => 'text',
                'required' => false,
                'in_list_view' => true,
                'sort_order' => 4,
                'description' => 'Primary contact phone number',
            ],

            // Classification
            [
                'fieldname' => 'customer_type',
                'label' => 'Customer Type',
                'fieldtype' => 'select',
                'options' => 'Individual,Corporate,Government,Non-Profit,Partner',
                'required' => true,
                'in_standard_filter' => true,
                'in_list_view' => true,
                'sort_order' => 5,
                'description' => 'Classification of customer type',
            ],
            [
                'fieldname' => 'priority_level',
                'label' => 'Priority Level',
                'fieldtype' => 'select',
                'options' => 'Low,Medium,High,VIP',
                'required' => false,
                'in_standard_filter' => true,
                'in_list_view' => true,
                'sort_order' => 6,
                'description' => 'Customer priority for support',
                'default_value' => 'Medium',
            ],

            // Address Information
            [
                'fieldname' => 'billing_address',
                'label' => 'Billing Address',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 7,
                'description' => 'Complete billing address',
            ],
            [
                'fieldname' => 'shipping_address',
                'label' => 'Shipping Address',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Complete shipping address',
            ],

            // Financial Information
            [
                'fieldname' => 'credit_limit',
                'label' => 'Credit Limit',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 9,
                'description' => 'Maximum credit amount allowed',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'payment_terms',
                'label' => 'Payment Terms',
                'fieldtype' => 'select',
                'options' => 'Net 15,Net 30,Net 45,Net 60,COD,Prepaid',
                'required' => false,
                'sort_order' => 10,
                'description' => 'Default payment terms',
                'default_value' => 'Net 30',
            ],

            // Status & Metadata
            [
                'fieldname' => 'is_active',
                'label' => 'Active Status',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 11,
                'description' => 'Whether customer is currently active',
            ],
            [
                'fieldname' => 'join_date',
                'label' => 'Join Date',
                'fieldtype' => 'date',
                'required' => true,
                'sort_order' => 12,
                'description' => 'Date when customer first joined',
            ],
            [
                'fieldname' => 'last_contact_date',
                'label' => 'Last Contact',
                'fieldtype' => 'datetime',
                'required' => false,
                'sort_order' => 13,
                'description' => 'Last time customer was contacted',
            ],

            // Additional Data
            [
                'fieldname' => 'tags',
                'label' => 'Tags',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 14,
                'description' => 'Customer tags and labels (JSON array)',
            ],
            [
                'fieldname' => 'notes',
                'label' => 'Internal Notes',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 15,
                'description' => 'Internal notes about the customer',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ Customer doctype created with {$doctype->doctypeFields()->count()} fields");
    }

    /**
     * Product DocType - E-commerce inventory
     */
    private function createProductDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'Product',
            'label' => 'Product Catalog',
            'description' => 'Complete product inventory management system',
            'is_active' => true,
            'icon' => 'package',
            'color' => '#10B981',
            'settings' => [
                'enable_workflow' => true,
                'track_changes' => true,
                'allow_duplicate' => false,
                'auto_name' => 'PROD-.####',
            ]
        ]);

        $fields = [
            // Basic Product Info
            [
                'fieldname' => 'sku',
                'label' => 'SKU',
                'fieldtype' => 'text',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 1,
                'description' => 'Stock Keeping Unit - unique product identifier',
            ],
            [
                'fieldname' => 'name',
                'label' => 'Product Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 2,
                'description' => 'Display name of the product',
            ],
            [
                'fieldname' => 'description',
                'label' => 'Description',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 3,
                'description' => 'Detailed product description for customers',
            ],
            [
                'fieldname' => 'short_description',
                'label' => 'Short Description',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 4,
                'description' => 'Brief product summary',
            ],

            // Categorization
            [
                'fieldname' => 'category',
                'label' => 'Category',
                'fieldtype' => 'select',
                'options' => 'Electronics,Clothing,Books,Home & Garden,Sports,Automotive,Health & Beauty,Toys & Games',
                'required' => true,
                'in_standard_filter' => true,
                'in_list_view' => true,
                'sort_order' => 5,
                'description' => 'Primary product category',
            ],
            [
                'fieldname' => 'brand',
                'label' => 'Brand',
                'fieldtype' => 'text',
                'required' => false,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 6,
                'description' => 'Product brand or manufacturer',
            ],
            [
                'fieldname' => 'model_number',
                'label' => 'Model Number',
                'fieldtype' => 'text',
                'required' => false,
                'sort_order' => 7,
                'description' => 'Manufacturer model number',
            ],

            // Pricing
            [
                'fieldname' => 'cost_price',
                'label' => 'Cost Price',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Product cost from supplier',
            ],
            [
                'fieldname' => 'selling_price',
                'label' => 'Selling Price',
                'fieldtype' => 'number',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 9,
                'description' => 'Regular selling price',
            ],
            [
                'fieldname' => 'sale_price',
                'label' => 'Sale Price',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 10,
                'description' => 'Discounted sale price',
            ],

            // Inventory
            [
                'fieldname' => 'stock_quantity',
                'label' => 'Stock Quantity',
                'fieldtype' => 'number',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 11,
                'description' => 'Current available stock',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'min_stock_level',
                'label' => 'Minimum Stock Level',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 12,
                'description' => 'Minimum stock before reorder alert',
                'default_value' => 5,
            ],
            [
                'fieldname' => 'max_stock_level',
                'label' => 'Maximum Stock Level',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 13,
                'description' => 'Maximum stock to maintain',
            ],

            // Physical Properties
            [
                'fieldname' => 'weight',
                'label' => 'Weight (kg)',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 14,
                'description' => 'Product weight in kilograms',
            ],
            [
                'fieldname' => 'dimensions',
                'label' => 'Dimensions',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 15,
                'description' => 'Product dimensions (L x W x H) in JSON format',
            ],

            // Digital Assets
            [
                'fieldname' => 'images',
                'label' => 'Product Images',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 16,
                'description' => 'Array of image URLs in JSON format',
            ],
            [
                'fieldname' => 'featured_image',
                'label' => 'Featured Image',
                'fieldtype' => 'image',
                'required' => false,
                'sort_order' => 17,
                'description' => 'Main product image',
            ],

            // Status & Flags
            [
                'fieldname' => 'is_active',
                'label' => 'Active',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 18,
                'description' => 'Product is available for sale',
            ],
            [
                'fieldname' => 'is_featured',
                'label' => 'Featured Product',
                'fieldtype' => 'checkbox',
                'default_value' => 0,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 19,
                'description' => 'Featured on homepage or promotions',
            ],
            [
                'fieldname' => 'requires_shipping',
                'label' => 'Requires Shipping',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'sort_order' => 20,
                'description' => 'Product needs physical shipping',
            ],

            // Metadata
            [
                'fieldname' => 'specifications',
                'label' => 'Technical Specifications',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 21,
                'description' => 'Detailed technical specs in JSON format',
            ],
            [
                'fieldname' => 'launch_date',
                'label' => 'Launch Date',
                'fieldtype' => 'date',
                'required' => false,
                'sort_order' => 22,
                'description' => 'Product launch or release date',
            ],
            [
                'fieldname' => 'discontinue_date',
                'label' => 'Discontinue Date',
                'fieldtype' => 'date',
                'required' => false,
                'sort_order' => 23,
                'description' => 'Date product will be discontinued',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ Product doctype created with {$doctype->doctypeFields()->count()} fields");
    }

    /**
     * Invoice DocType - Financial management
     */
    private function createInvoiceDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'Invoice',
            'label' => 'Invoice Management',
            'description' => 'Complete invoice and billing system',
            'is_active' => true,
            'icon' => 'file-text',
            'color' => '#F59E0B',
            'settings' => [
                'enable_workflow' => true,
                'track_changes' => true,
                'auto_name' => 'INV-.YYYY.-.####',
                'number_series' => 'INV-',
            ]
        ]);

        $fields = [
            // Header Information
            [
                'fieldname' => 'invoice_number',
                'label' => 'Invoice Number',
                'fieldtype' => 'text',
                'required' => true,
                'unique' => true,
                'in_list_view' => true,
                'sort_order' => 1,
                'description' => 'Unique invoice identifier',
            ],
            [
                'fieldname' => 'customer_name',
                'label' => 'Customer Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 2,
                'description' => 'Name of the customer being billed',
            ],
            [
                'fieldname' => 'customer_email',
                'label' => 'Customer Email',
                'fieldtype' => 'email',
                'required' => true,
                'sort_order' => 3,
                'description' => 'Customer email for invoice delivery',
            ],

            // Dates
            [
                'fieldname' => 'invoice_date',
                'label' => 'Invoice Date',
                'fieldtype' => 'date',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 4,
                'description' => 'Date the invoice was created',
            ],
            [
                'fieldname' => 'due_date',
                'label' => 'Due Date',
                'fieldtype' => 'date',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 5,
                'description' => 'Payment due date',
            ],

            // Financial Details
            [
                'fieldname' => 'subtotal',
                'label' => 'Subtotal',
                'fieldtype' => 'number',
                'required' => true,
                'sort_order' => 6,
                'description' => 'Invoice subtotal before tax',
            ],
            [
                'fieldname' => 'tax_rate',
                'label' => 'Tax Rate (%)',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 7,
                'description' => 'Tax rate percentage',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'tax_amount',
                'label' => 'Tax Amount',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Total tax amount',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'discount_amount',
                'label' => 'Discount Amount',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 9,
                'description' => 'Total discount applied',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'total_amount',
                'label' => 'Total Amount',
                'fieldtype' => 'number',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 10,
                'description' => 'Final invoice total',
            ],

            // Status & Payment
            [
                'fieldname' => 'status',
                'label' => 'Invoice Status',
                'fieldtype' => 'select',
                'options' => 'Draft,Sent,Paid,Overdue,Cancelled,Refunded',
                'required' => true,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 11,
                'description' => 'Current invoice status',
                'default_value' => 'Draft',
            ],
            [
                'fieldname' => 'payment_method',
                'label' => 'Payment Method',
                'fieldtype' => 'select',
                'options' => 'Cash,Credit Card,Bank Transfer,Check,PayPal,Stripe',
                'required' => false,
                'in_standard_filter' => true,
                'sort_order' => 12,
                'description' => 'Method of payment',
            ],
            [
                'fieldname' => 'paid_date',
                'label' => 'Paid Date',
                'fieldtype' => 'datetime',
                'required' => false,
                'sort_order' => 13,
                'description' => 'Date and time payment was received',
            ],

            // Line Items & Additional Data
            [
                'fieldname' => 'line_items',
                'label' => 'Line Items',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 14,
                'description' => 'Invoice line items in JSON format',
            ],
            [
                'fieldname' => 'billing_address',
                'label' => 'Billing Address',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 15,
                'description' => 'Customer billing address',
            ],
            [
                'fieldname' => 'notes',
                'label' => 'Notes',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 16,
                'description' => 'Additional notes or terms',
            ],
            [
                'fieldname' => 'reference_number',
                'label' => 'Reference Number',
                'fieldtype' => 'text',
                'required' => false,
                'sort_order' => 17,
                'description' => 'Customer or PO reference number',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ Invoice doctype created with {$doctype->doctypeFields()->count()} fields");
    }

    /**
     * BlogPost DocType - Content management
     */
    private function createBlogPostDoctype(): void
    {
        $doctype = Doctype::create([
            'name' => 'BlogPost',
            'label' => 'Blog Post Management',
            'description' => 'Complete blog and content management system',
            'is_active' => true,
            'icon' => 'edit',
            'color' => '#8B5CF6',
            'settings' => [
                'enable_workflow' => true,
                'track_changes' => true,
                'auto_name' => 'POST-.YYYY.-.####',
                'enable_comments' => true,
            ]
        ]);

        $fields = [
            // Content
            [
                'fieldname' => 'title',
                'label' => 'Post Title',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 1,
                'description' => 'Blog post title',
            ],
            [
                'fieldname' => 'slug',
                'label' => 'URL Slug',
                'fieldtype' => 'text',
                'required' => true,
                'unique' => true,
                'sort_order' => 2,
                'description' => 'URL-friendly version of title',
            ],
            [
                'fieldname' => 'excerpt',
                'label' => 'Excerpt',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 3,
                'description' => 'Short description or summary',
            ],
            [
                'fieldname' => 'content',
                'label' => 'Content',
                'fieldtype' => 'textarea',
                'required' => true,
                'sort_order' => 4,
                'description' => 'Main blog post content (HTML/Markdown)',
            ],

            // Classification
            [
                'fieldname' => 'category',
                'label' => 'Category',
                'fieldtype' => 'select',
                'options' => 'Technology,Business,Lifestyle,Travel,Food,Health,Education,Entertainment',
                'required' => true,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 5,
                'description' => 'Primary blog category',
            ],
            [
                'fieldname' => 'tags',
                'label' => 'Tags',
                'fieldtype' => 'json',
                'required' => false,
                'sort_order' => 6,
                'description' => 'Post tags in JSON array format',
            ],

            // Author & Publishing
            [
                'fieldname' => 'author_name',
                'label' => 'Author Name',
                'fieldtype' => 'text',
                'required' => true,
                'in_list_view' => true,
                'sort_order' => 7,
                'description' => 'Post author name',
            ],
            [
                'fieldname' => 'author_email',
                'label' => 'Author Email',
                'fieldtype' => 'email',
                'required' => false,
                'sort_order' => 8,
                'description' => 'Author email address',
            ],
            [
                'fieldname' => 'status',
                'label' => 'Publication Status',
                'fieldtype' => 'select',
                'options' => 'Draft,Review,Published,Archived',
                'required' => true,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 9,
                'description' => 'Current publication status',
                'default_value' => 'Draft',
            ],
            [
                'fieldname' => 'published_date',
                'label' => 'Published Date',
                'fieldtype' => 'datetime',
                'required' => false,
                'in_list_view' => true,
                'sort_order' => 10,
                'description' => 'Date and time of publication',
            ],

            // SEO & Social
            [
                'fieldname' => 'meta_title',
                'label' => 'Meta Title',
                'fieldtype' => 'text',
                'required' => false,
                'sort_order' => 11,
                'description' => 'SEO meta title (max 60 chars)',
            ],
            [
                'fieldname' => 'meta_description',
                'label' => 'Meta Description',
                'fieldtype' => 'textarea',
                'required' => false,
                'sort_order' => 12,
                'description' => 'SEO meta description (max 160 chars)',
            ],
            [
                'fieldname' => 'featured_image',
                'label' => 'Featured Image',
                'fieldtype' => 'image',
                'required' => false,
                'sort_order' => 13,
                'description' => 'Main blog post image',
            ],
            [
                'fieldname' => 'social_image',
                'label' => 'Social Media Image',
                'fieldtype' => 'image',
                'required' => false,
                'sort_order' => 14,
                'description' => 'Image for social media sharing',
            ],

            // Engagement & Analytics
            [
                'fieldname' => 'view_count',
                'label' => 'View Count',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 15,
                'description' => 'Number of times post was viewed',
                'default_value' => 0,
            ],
            [
                'fieldname' => 'reading_time',
                'label' => 'Reading Time (minutes)',
                'fieldtype' => 'number',
                'required' => false,
                'sort_order' => 16,
                'description' => 'Estimated reading time in minutes',
            ],
            [
                'fieldname' => 'is_featured',
                'label' => 'Featured Post',
                'fieldtype' => 'checkbox',
                'default_value' => 0,
                'in_list_view' => true,
                'in_standard_filter' => true,
                'sort_order' => 17,
                'description' => 'Featured on homepage or special sections',
            ],
            [
                'fieldname' => 'allow_comments',
                'label' => 'Allow Comments',
                'fieldtype' => 'checkbox',
                'default_value' => 1,
                'sort_order' => 18,
                'description' => 'Enable comments on this post',
            ],
        ];

        foreach ($fields as $fieldData) {
            DoctypeField::create([
                'doctype_id' => $doctype->id,
                ...$fieldData
            ]);
        }

        $this->command->info("✓ BlogPost doctype created with {$doctype->doctypeFields()->count()} fields");
    }
}
