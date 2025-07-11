// Core types for the doctypes package
export interface DoctypeField {
    id?: number;
    fieldname: string;
    label: string;
    fieldtype: FieldType;
    options?: string;
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
    is_active?: boolean;
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

// Form Schema for dynamic forms
export interface FormField {
    name: string;
    label: string;
    type: FieldType;
    required: boolean;
    description?: string;
    options?: any[];
    default_value?: any;
}

export interface DoctypeFormSchema {
    doctype: string;
    title: string;
    description?: string;
    fields: FormField[];
}

// API Request/Response types
export interface DoctypeCreateRequest {
    name: string;
    label: string;
    description?: string;
    fields: Omit<DoctypeField, 'id'>[];
}

export interface DoctypeUpdateRequest extends Partial<DoctypeCreateRequest> {
    id: number;
}

export interface DoctypeResponse {
    data: Doctype;
    message?: string;
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

export interface DoctypeSchemaResponse {
    data: DoctypeFormSchema;
    message?: string;
}

export interface DoctypeGeneratorResponse {
    message: string;
    files: string[];
}

// Generator request types
export interface FileGenerationRequest {
    doctype: string;
    types: ('model' | 'controller' | 'request' | 'resource' | 'migration')[];
    options?: {
        module?: string;
        force?: boolean;
    };
}