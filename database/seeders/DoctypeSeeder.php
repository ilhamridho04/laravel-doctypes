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

        // Sample Product DocType
        $product = Doctype::create([
            'name' => 'product',
            'label' => 'Product',
            'description' => 'Product catalog management',
            'is_active' => true,
            'is_system' => false,
            'icon' => 'package',
            'color' => '#f59e0b',
        ]);

        $product->addField([
            'fieldname' => 'name',
            'label' => 'Product Name',
            'fieldtype' => 'text',
            'required' => true,
            'in_list_view' => true,
            'sort_order' => 1,
        ]);

        $product->addField([
            'fieldname' => 'sku',
            'label' => 'SKU',
            'fieldtype' => 'text',
            'required' => true,
            'unique' => true,
            'in_list_view' => true,
            'sort_order' => 2,
        ]);

        $product->addField([
            'fieldname' => 'price',
            'label' => 'Price',
            'fieldtype' => 'number',
            'options' => [
                'min' => 0,
                'step' => 0.01
            ],
            'required' => true,
            'in_list_view' => true,
            'sort_order' => 3,
        ]);

        $product->addField([
            'fieldname' => 'category',
            'label' => 'Category',
            'fieldtype' => 'select',
            'options' => [
                'options' => ['electronics', 'clothing', 'books', 'home', 'sports'],
                'placeholder' => 'Select category'
            ],
            'required' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'sort_order' => 4,
        ]);

        $product->addField([
            'fieldname' => 'description',
            'label' => 'Description',
            'fieldtype' => 'textarea',
            'required' => false,
            'sort_order' => 5,
        ]);

        $product->addField([
            'fieldname' => 'in_stock',
            'label' => 'In Stock',
            'fieldtype' => 'checkbox',
            'required' => false,
            'default_value' => true,
            'in_list_view' => true,
            'in_standard_filter' => true,
            'sort_order' => 6,
        ]);
    }
}
