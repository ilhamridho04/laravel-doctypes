export interface DoctypeField {
    id?: number;
    fieldname: string;
    label: string;
    fieldtype: FieldType;
    options?: Record<string, any>;
    required?: boolean;
    unique?: boolean;
    in_list_view?: boolean;
    in_standard_filter?: boolean;
    description?: string;
    default_value?: any;
    sort_order?: number;
}

export interface Doctype {
    id?: number;
    name: string;
    label: string;
    description?: string;
    fields: DoctypeField[];
    settings?: Record<string, any>;
    is_active?: boolean;
    is_system?: boolean;
    icon?: string;
    color?: string;
    created_at?: string;
    updated_at?: string;
    fields_count?: number;
}

export type FieldType =
    | 'text'
    | 'textarea'
    | 'number'
    | 'email'
    | 'password'
    | 'select'
    | 'checkbox'
    | 'date'
    | 'datetime'
    | 'time'
    | 'file'
    | 'image'
    | 'json';

export interface DoctypeFormSchema {
    name: string;
    label: string;
    type: FieldType;
    required?: boolean;
    unique?: boolean;
    options?: Record<string, any>;
    description?: string;
    default_value?: any;
    in_list_view?: boolean;
    in_standard_filter?: boolean;
}

export interface DoctypeCreateRequest {
    name: string;
    label: string;
    description?: string;
    fields?: DoctypeField[];
    settings?: Record<string, any>;
    is_active?: boolean;
    icon?: string;
    color?: string;
}

export interface DoctypeUpdateRequest extends Partial<DoctypeCreateRequest> {
    id: number;
}

export interface DoctypeListResponse {
    data: Doctype[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

export interface DoctypeResponse {
    data: Doctype;
    message?: string;
}

export interface DoctypeSchemaResponse {
    schema: DoctypeFormSchema[];
}

export interface DoctypeFilters {
    search?: string;
    active?: boolean;
    system?: boolean;
    per_page?: number;
    page?: number;
}

export interface FieldValidationRule {
    type: 'required' | 'unique' | 'min' | 'max' | 'email' | 'regex';
    value?: any;
    message?: string;
}

export interface FormFieldConfig {
    field: DoctypeField;
    value: any;
    error?: string;
    readonly?: boolean;
    hidden?: boolean;
}

export interface GeneratedFormData {
    [fieldname: string]: any;
}

export interface FormValidationErrors {
    [fieldname: string]: string[];
}

export interface DoctypeFieldOptions {
    // For select fields
    options?: string[] | { label: string; value: any }[];
    placeholder?: string;

    // For number fields
    min?: number;
    max?: number;
    step?: number;

    // For text fields
    minLength?: number;
    maxLength?: number;
    pattern?: string;

    // For file fields
    accept?: string;
    maxSize?: number;

    // For datetime fields
    format?: string;

    // Custom validation
    validation?: FieldValidationRule[];
}

export interface DoctypeFormState {
    loading: boolean;
    saving: boolean;
    errors: FormValidationErrors;
    data: GeneratedFormData;
    schema: DoctypeFormSchema[];
    doctype?: Doctype;
}
