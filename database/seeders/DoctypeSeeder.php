<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Doctypes\Models\Doctype;

class DoctypeSeeder extends Seeder
{
    public function run(): void
    {
        // Sample User Profile DocType
        $userProfile = Doctype::create([
            'name' => 'user_profile',
            'label' => 'User Profile',
            'description' => 'User profile information',
            'is_active' => true,
            'is_system' => false,
            'icon' => 'user',
            'color' => '#3b82f6',
        ]);

        $userProfile->addField([
            'fieldname' => 'first_name',
            'label' => 'First Name',
            'fieldtype' => 'text',
            'required' => true,
            'in_list_view' => true,
            'sort_order' => 1,
        ]);

        $userProfile->addField([
            'fieldname' => 'last_name',
            'label' => 'Last Name',
            'fieldtype' => 'text',
            'required' => true,
            'in_list_view' => true,
            'sort_order' => 2,
        ]);

        $userProfile->addField([
            'fieldname' => 'email',
            'label' => 'Email Address',
            'fieldtype' => 'email',
            'required' => true,
            'unique' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'sort_order' => 3,
        ]);

        $userProfile->addField([
            'fieldname' => 'phone',
            'label' => 'Phone Number',
            'fieldtype' => 'text',
            'required' => false,
            'in_list_view' => true,
            'sort_order' => 4,
        ]);

        $userProfile->addField([
            'fieldname' => 'date_of_birth',
            'label' => 'Date of Birth',
            'fieldtype' => 'date',
            'required' => false,
            'sort_order' => 5,
        ]);

        $userProfile->addField([
            'fieldname' => 'gender',
            'label' => 'Gender',
            'fieldtype' => 'select',
            'options' => [
                'options' => ['male', 'female', 'other'],
                'placeholder' => 'Select gender'
            ],
            'required' => false,
            'in_standard_filter' => true,
            'sort_order' => 6,
        ]);

        $userProfile->addField([
            'fieldname' => 'bio',
            'label' => 'Biography',
            'fieldtype' => 'textarea',
            'description' => 'Tell us about yourself',
            'required' => false,
            'sort_order' => 7,
        ]);

        // Sample Task DocType
        $task = Doctype::create([
            'name' => 'task',
            'label' => 'Task',
            'description' => 'Task management system',
            'is_active' => true,
            'is_system' => false,
            'icon' => 'check-square',
            'color' => '#10b981',
        ]);

        $task->addField([
            'fieldname' => 'title',
            'label' => 'Task Title',
            'fieldtype' => 'text',
            'required' => true,
            'in_list_view' => true,
            'sort_order' => 1,
        ]);

        $task->addField([
            'fieldname' => 'description',
            'label' => 'Description',
            'fieldtype' => 'textarea',
            'required' => false,
            'sort_order' => 2,
        ]);

        $task->addField([
            'fieldname' => 'status',
            'label' => 'Status',
            'fieldtype' => 'select',
            'options' => [
                'options' => ['pending', 'in_progress', 'completed', 'cancelled'],
                'placeholder' => 'Select status'
            ],
            'required' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'default_value' => 'pending',
            'sort_order' => 3,
        ]);

        $task->addField([
            'fieldname' => 'priority',
            'label' => 'Priority',
            'fieldtype' => 'select',
            'options' => [
                'options' => ['low', 'medium', 'high', 'urgent'],
                'placeholder' => 'Select priority'
            ],
            'required' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'default_value' => 'medium',
            'sort_order' => 4,
        ]);

        $task->addField([
            'fieldname' => 'due_date',
            'label' => 'Due Date',
            'fieldtype' => 'date',
            'required' => false,
            'in_list_view' => true,
            'sort_order' => 5,
        ]);

        $task->addField([
            'fieldname' => 'assigned_to',
            'label' => 'Assigned To',
            'fieldtype' => 'email',
            'required' => false,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'sort_order' => 6,
        ]);

        $task->addField([
            'fieldname' => 'estimated_hours',
            'label' => 'Estimated Hours',
            'fieldtype' => 'number',
            'options' => [
                'min' => 0,
                'step' => 0.5
            ],
            'required' => false,
            'sort_order' => 7,
        ]);

        $task->addField([
            'fieldname' => 'is_billable',
            'label' => 'Is Billable',
            'fieldtype' => 'checkbox',
            'required' => false,
            'default_value' => false,
            'sort_order' => 8,
        ]);

        // Advanced Product DocType with various field types
        $product = Doctype::create([
            'name' => 'product',
            'label' => 'Product',
            'description' => 'Product catalog management',
            'is_active' => true,
            'is_system' => false,
            'icon' => 'cube',
            'color' => '#10b981',
        ]);

        $product->addField([
            'fieldname' => 'name',
            'label' => 'Product Name',
            'fieldtype' => 'Data',
            'required' => true,
            'in_list_view' => true,
            'options' => json_encode([
                'placeholder' => 'Enter product name',
                'max_length' => 100
            ]),
            'sort_order' => 1,
        ]);

        $product->addField([
            'fieldname' => 'description',
            'label' => 'Description',
            'fieldtype' => 'Long Text',
            'required' => false,
            'options' => json_encode([
                'placeholder' => 'Enter product description',
                'rows' => 4
            ]),
            'sort_order' => 2,
        ]);

        $product->addField([
            'fieldname' => 'price',
            'label' => 'Price',
            'fieldtype' => 'Currency',
            'required' => true,
            'in_list_view' => true,
            'options' => json_encode([
                'min' => 0,
                'step' => 0.01,
                'placeholder' => '0.00'
            ]),
            'sort_order' => 3,
        ]);

        $product->addField([
            'fieldname' => 'category',
            'label' => 'Category',
            'fieldtype' => 'Select',
            'required' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'options' => json_encode([
                'options' => [
                    'electronics' => 'Electronics',
                    'clothing' => 'Clothing',
                    'books' => 'Books',
                    'home' => 'Home & Garden',
                    'sports' => 'Sports & Outdoors'
                ]
            ]),
            'sort_order' => 4,
        ]);

        $product->addField([
            'fieldname' => 'is_featured',
            'label' => 'Featured Product',
            'fieldtype' => 'Check',
            'required' => false,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'default_value' => 0,
            'sort_order' => 5,
        ]);

        $product->addField([
            'fieldname' => 'launch_date',
            'label' => 'Launch Date',
            'fieldtype' => 'Date',
            'required' => false,
            'in_list_view' => true,
            'sort_order' => 6,
        ]);

        $product->addField([
            'fieldname' => 'specifications',
            'label' => 'Specifications',
            'fieldtype' => 'JSON',
            'required' => false,
            'options' => json_encode([
                'placeholder' => '{"key": "value"}',
                'rows' => 6
            ]),
            'sort_order' => 7,
        ]);

        $product->addField([
            'fieldname' => 'product_image',
            'label' => 'Product Image',
            'fieldtype' => 'Attach Image',
            'required' => false,
            'options' => json_encode([
                'accept' => 'image/*',
                'multiple' => false
            ]),
            'sort_order' => 8,
        ]);

        // Blog Post DocType
        $blogPost = Doctype::create([
            'name' => 'blog_post',
            'label' => 'Blog Post',
            'description' => 'Blog post management system',
            'is_active' => true,
            'is_system' => false,
            'icon' => 'document-text',
            'color' => '#8b5cf6',
        ]);

        $blogPost->addField([
            'fieldname' => 'title',
            'label' => 'Title',
            'fieldtype' => 'Data',
            'required' => true,
            'in_list_view' => true,
            'options' => json_encode([
                'placeholder' => 'Enter blog post title',
                'max_length' => 150
            ]),
            'sort_order' => 1,
        ]);

        $blogPost->addField([
            'fieldname' => 'slug',
            'label' => 'URL Slug',
            'fieldtype' => 'Data',
            'required' => true,
            'unique' => true,
            'options' => json_encode([
                'placeholder' => 'url-friendly-slug',
                'pattern' => '^[a-z0-9-]+$'
            ]),
            'sort_order' => 2,
        ]);

        $blogPost->addField([
            'fieldname' => 'content',
            'label' => 'Content',
            'fieldtype' => 'Long Text',
            'required' => true,
            'options' => json_encode([
                'placeholder' => 'Write your blog post content here...',
                'rows' => 10
            ]),
            'sort_order' => 3,
        ]);

        $blogPost->addField([
            'fieldname' => 'status',
            'label' => 'Status',
            'fieldtype' => 'Select',
            'required' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'default_value' => 'draft',
            'options' => json_encode([
                'options' => [
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived'
                ]
            ]),
            'sort_order' => 4,
        ]);

        $blogPost->addField([
            'fieldname' => 'publish_date',
            'label' => 'Publish Date',
            'fieldtype' => 'Datetime',
            'required' => false,
            'in_list_view' => true,
            'sort_order' => 5,
        ]);

        $blogPost->addField([
            'fieldname' => 'tags',
            'label' => 'Tags',
            'fieldtype' => 'Data',
            'required' => false,
            'options' => json_encode([
                'placeholder' => 'Enter comma-separated tags'
            ]),
            'sort_order' => 6,
        ]);

        $blogPost->addField([
            'fieldname' => 'featured_image',
            'label' => 'Featured Image',
            'fieldtype' => 'Attach Image',
            'required' => false,
            'options' => json_encode([
                'accept' => 'image/jpeg,image/png,image/webp',
                'multiple' => false
            ]),
            'sort_order' => 7,
        ]);
    }
}
