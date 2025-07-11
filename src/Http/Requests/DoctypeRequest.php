<?php

namespace Ngodingskuyy\Doctypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'nullable|array',
            'fields.*.fieldname' => 'required|string|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.fieldtype' => 'required|string|in:text,textarea,number,email,password,select,checkbox,date,datetime,time,file,image,json',
            'fields.*.options' => 'nullable|array',
            'fields.*.required' => 'boolean',
            'fields.*.unique' => 'boolean',
            'fields.*.in_list_view' => 'boolean',
            'fields.*.in_standard_filter' => 'boolean',
            'fields.*.description' => 'nullable|string',
            'fields.*.default_value' => 'nullable|string',
            'fields.*.sort_order' => 'integer|min:0',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ];

        // Make name unique on create or update (except current record)
        if ($this->isMethod('POST')) {
            $rules['name'] .= '|unique:doctypes,name';
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] .= '|unique:doctypes,name,' . $this->route('doctype')->id;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'The name must start with a letter and contain only letters, numbers, and underscores.',
            'name.unique' => 'A doctype with this name already exists.',
            'fields.*.fieldname.regex' => 'Field name must start with a letter and contain only letters, numbers, and underscores.',
            'fields.*.fieldtype.in' => 'Invalid field type selected.',
            'color.max' => 'Color must be a valid hex color code.',
        ];
    }
}
