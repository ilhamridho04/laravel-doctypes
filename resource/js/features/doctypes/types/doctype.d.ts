import type { Ref, ComputedRef } from 'vue';

export interface DoctypeField {
    id?: number;
    fieldname: string;
    label: string;
    fieldtype: FieldType;
    options?: DoctypeFieldOptions;
    required?: boolean;
    unique?: boolean;
    in_list_view?: boolean;
    in_standard_filter?: boolean;
    description?: string;
    default_value?: any;
    sort_order?: number;
    // Advanced properties
    validation_rules?: FieldValidationRule[];
    depends_on?: string; // Conditional field display
    read_only?: boolean;
    hidden?: boolean;
}

export interface Doctype {
    id?: number;
    name: string;
    label: string;
    description?: string;
    fields: DoctypeField[];
    settings?: DoctypeSettings;
    is_active?: boolean;
    is_system?: boolean;
    icon?: string;
    color?: string;
    created_at?: string;
    updated_at?: string;
    fields_count?: number;
    // Generator specific properties
    module?: string;
    table_name?: string;
    naming_series?: string;
    title_field?: string;
    search_fields?: string[];
    sort_fields?: string[];
    // Permission settings
    permissions?: DoctypePermissions;
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

export interface FormFieldValidation {
    required?: boolean;
    minLength?: number;
    maxLength?: number;
    min?: number;
    max?: number;
    pattern?: string;
    type?: 'email' | 'url' | 'number' | 'date' | 'time';
    custom?: string; // Custom validation function name
}

// Form Schema Generation Types
export interface DoctypeFormSchema {
    doctype: string;
    title: string;
    description?: string;
    fields: FormField[];
    layout?: FormLayout;
    validation_rules?: Record<string, FieldValidationRule[]>;
    settings?: FormSettings;
}

export interface FormField {
    name: string;
    label: string;
    type: FieldType;
    required: boolean;
    description?: string;
    placeholder?: string;
    options?: any[];
    validation?: FormFieldValidation;
    default_value?: any;

    // UI properties
    min?: number;
    max?: number;
    step?: number;
    rows?: number;
    accept?: string;
    multiple?: boolean;
    class?: string;
    style?: string;
    width?: 'full' | 'half' | 'third' | 'quarter' | string;

    // Conditional logic
    depends_on?: string;
    read_only?: boolean;
    hidden?: boolean;

    // Advanced properties
    fetch_from?: string;
    link_doctype?: string;
    link_filters?: Record<string, any>;
}

export interface FormLayout {
    type: 'grid' | 'tabs' | 'sections';
    columns?: number;
    sections?: FormSection[];
    tabs?: FormTab[];
}

export interface FormSection {
    title: string;
    description?: string;
    fields: string[];
    collapsible?: boolean;
    columns?: number;
}

export interface FormTab {
    title: string;
    description?: string;
    sections: FormSection[];
}

export interface FormSettings {
    show_attachments?: boolean;
    show_comments?: boolean;
    show_assignments?: boolean;
    show_tags?: boolean;
    allow_print?: boolean;
    allow_email?: boolean;
    allow_copy?: boolean;
    show_timeline?: boolean;
}

export interface FieldValidationRule {
    type: 'required' | 'unique' | 'min' | 'max' | 'email' | 'regex' | 'custom';
    value?: any;
    message?: string;
    function?: string; // Custom validation function name
}

export interface FormFieldConfig {
    field: DoctypeField;
    value: any;
    error?: string;
    readonly?: boolean;
    hidden?: boolean;
    depends_on_value?: any;
}

export interface GeneratedFormData {
    [fieldname: string]: any;
}

export interface FormValidationErrors {
    [fieldname: string]: string[];
}

export interface DoctypeSettings {
    auto_name?: 'hash' | 'prompt' | 'field' | 'naming_series';
    naming_series_options?: string[];
    allow_rename?: boolean;
    is_submittable?: boolean;
    is_child?: boolean;
    istable?: boolean;
    track_changes?: boolean;
    show_name_in_global_search?: boolean;
    default_print_format?: string;
    max_attachments?: number;
    description?: string;
    documentation?: string;
}

export interface DoctypePermissions {
    create?: boolean;
    read?: boolean;
    write?: boolean;
    delete?: boolean;
    submit?: boolean;
    cancel?: boolean;
    amend?: boolean;
    print?: boolean;
    email?: boolean;
    export?: boolean;
    import?: boolean;
    share?: boolean;
    roles?: string[];
}

export interface DoctypeFieldOptions {
    // For select fields
    options?: string[] | SelectOption[];
    placeholder?: string;

    // For number fields  
    min?: number;
    max?: number;
    step?: number;
    precision?: number;

    // For text fields
    minLength?: number;
    maxLength?: number;
    pattern?: string;

    // For file fields
    accept?: string;
    maxSize?: number; // in MB
    multiple?: boolean;

    // For datetime fields
    format?: string;
    show_time?: boolean;

    // For textarea fields
    rows?: number;
    cols?: number;

    // UI specific
    class?: string;
    style?: string;
    width?: string;

    // Custom validation
    validation?: FieldValidationRule[];

    // Conditional logic
    depends_on?: string;
    mandatory_depends_on?: string;
    read_only_depends_on?: string;

    // Link field specific
    link_doctype?: string;
    link_filters?: Record<string, any>;

    // Custom field properties
    allow_in_quick_entry?: boolean;
    bold?: boolean;
    collapsible?: boolean;
    collapsible_depends_on?: string;
    columns?: number;
    fetch_from?: string;
    fetch_if_empty?: boolean;
    ignore_user_permissions?: boolean;
    ignore_xss_filter?: boolean;
    in_global_search?: boolean;
    in_preview?: boolean;
    length?: number;
    no_copy?: boolean;
    permlevel?: number;
    print_hide?: boolean;
    print_hide_if_no_value?: boolean;
    print_width?: string;
    report_hide?: boolean;
    reqd?: boolean;
    search_index?: boolean;
    translatable?: boolean;
}

export interface SelectOption {
    label: string;
    value: any;
    disabled?: boolean;
    description?: string;
}

// Dynamic form management
export type DynamicFormData = Record<string, any>;
export type FormErrors = Record<string, string>;

// Generator Response Types
export interface DoctypeGeneratorResponse {
    success: boolean;
    message: string;
    generated_files: GeneratedFile[];
    doctype: string;
    table_name: string;
}

export interface GeneratedFile {
    type: 'model' | 'controller' | 'request' | 'resource' | 'migration' | 'seeder';
    path: string;
    content?: string;
    created: boolean;
}

// API Response Types Enhanced
export interface DoctypeSchemaResponse {
    success: boolean;
    data: {
        doctype: Doctype;
        schema: DoctypeFormSchema;
    };
    message?: string;
}

export interface DoctypeGenerationResponse {
    success: boolean;
    data: {
        doctype: string;
        generated: GeneratedFile[];
        table_name: string;
    };
    message?: string;
}

export interface DoctypeCreateRequest {
    name: string;
    label: string;
    description?: string;
    fields?: DoctypeField[];
    settings?: DoctypeSettings;
    is_active?: boolean;
    icon?: string;
    color?: string;
    module?: string;
    table_name?: string;
    naming_series?: string;
    title_field?: string;
    search_fields?: string[];
    sort_fields?: string[];
    permissions?: DoctypePermissions;
}

export interface DoctypeUpdateRequest extends Partial<DoctypeCreateRequest> {
    id: number;
}

export interface DoctypeListResponse {
    success: boolean;
    data: Doctype[];
    meta: PaginationMeta;
    message?: string;
}

export interface DoctypeResponse {
    success: boolean;
    data: Doctype;
    message?: string;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links?: PaginationLink[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface DoctypeFilters {
    search?: string;
    active?: boolean;
    system?: boolean;
    module?: string;
    per_page?: number;
    page?: number;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
}

// Dynamic Model CRUD Types
export interface DynamicModelResponse {
    success: boolean;
    data: Record<string, any>;
    message?: string;
}

export interface DynamicModelListResponse {
    success: boolean;
    data: Record<string, any>[];
    meta: PaginationMeta;
    message?: string;
}

export interface DynamicModelCreateRequest {
    doctype: string;
    data: Record<string, any>;
}

export interface DynamicModelUpdateRequest {
    doctype: string;
    id: number | string;
    data: Record<string, any>;
}

export interface DynamicModelFilters {
    doctype: string;
    filters?: Record<string, any>;
    search?: string;
    search_fields?: string[];
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
    per_page?: number;
    page?: number;
}

// File Generation Types
export interface FileGenerationRequest {
    doctype: string;
    types: ('model' | 'controller' | 'request' | 'resource' | 'migration' | 'seeder')[];
    options?: FileGenerationOptions;
}

export interface FileGenerationOptions {
    force?: boolean;
    module?: string;
    namespace?: string;
    table_name?: string;
    relationships?: Record<string, any>;
    with_factory?: boolean;
    with_seeder?: boolean;
}

// Utility Types
export type DoctypeEventType = 'created' | 'updated' | 'deleted' | 'generated';

export interface DoctypeEvent {
    type: DoctypeEventType;
    doctype: string;
    data?: any;
    timestamp: string;
}

// Error Handling Types
export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
    code?: string;
    status?: number;
}

export interface ValidationError {
    field: string;
    message: string;
    rule: string;
    value?: any;
}

// Vue Composable State Management Types
export interface UseDoctypesState {
    doctypes: Ref<Doctype[]>;
    currentDoctype: Ref<Doctype | null>;
    loading: Ref<boolean>;
    error: Ref<string | null>;
    meta: Ref<PaginationMeta>;
    filters: Ref<DoctypeFilters>;
}

export interface UseDoctypesActions {
    fetchDoctypes: (filters?: DoctypeFilters) => Promise<void>;
    getDoctypeById: (id: number) => Promise<Doctype>;
    getDoctypeByName: (name: string) => Promise<Doctype>;
    createDoctype: (data: DoctypeCreateRequest) => Promise<Doctype>;
    updateDoctype: (id: number, data: DoctypeUpdateRequest) => Promise<Doctype>;
    deleteDoctype: (id: number) => Promise<void>;
    generateSchema: (doctypeName: string) => Promise<DoctypeFormSchema>;
    generateFiles: (request: FileGenerationRequest) => Promise<DoctypeGeneratorResponse>;
    refreshDoctypes: () => Promise<void>;
    setCurrentDoctype: (doctype: Doctype | null) => void;
    clearError: () => void;
}

export interface UseDynamicFormState {
    formData: Ref<DynamicFormData>;
    originalData: Ref<DynamicFormData>;
    schema: Ref<DoctypeFormSchema | null>;
    doctype: Ref<Doctype | null>;
    loading: Ref<boolean>;
    saving: Ref<boolean>;
    errors: Ref<FormErrors>;
    isDirty: ComputedRef<boolean>;
    isValid: ComputedRef<boolean>;
    mode: Ref<'create' | 'edit' | 'view'>;
}

export interface UseDynamicFormActions {
    loadSchema: (doctypeName: string) => Promise<void>;
    loadRecord: (doctypeName: string, recordId: number | string) => Promise<void>;
    resetForm: () => void;
    validateField: (fieldName: string) => boolean;
    validateForm: () => boolean;
    saveRecord: () => Promise<any>;
    updateField: (fieldName: string, value: any) => void;
    setFieldError: (fieldName: string, error: string) => void;
    clearFieldError: (fieldName: string) => void;
    clearAllErrors: () => void;
    setMode: (mode: 'create' | 'edit' | 'view') => void;
}

// Field Renderer Props
export interface FieldRendererProps {
    field: FormField;
    modelValue: any;
    error?: string;
    readonly?: boolean;
    hidden?: boolean;
    class?: string;
    style?: string;
}

export interface FieldRendererEmits {
    'update:modelValue': [value: any];
    'blur': [event: Event];
    'focus': [event: Event];
    'change': [value: any];
    'input': [value: any];
}

// Form Builder Types
export interface FormBuilderState {
    doctype: Ref<Partial<Doctype>>;
    fields: Ref<DoctypeField[]>;
    activeField: Ref<DoctypeField | null>;
    fieldTypes: Ref<FieldTypeOption[]>;
    isDirty: ComputedRef<boolean>;
    isValid: ComputedRef<boolean>;
}

export interface FormBuilderActions {
    addField: (type?: FieldType) => void;
    removeField: (index: number) => void;
    moveField: (fromIndex: number, toIndex: number) => void;
    duplicateField: (index: number) => void;
    selectField: (field: DoctypeField) => void;
    updateField: (index: number, updates: Partial<DoctypeField>) => void;
    updateDoctype: (updates: Partial<Doctype>) => void;
    resetForm: () => void;
    saveDoctype: () => Promise<Doctype>;
    previewForm: () => void;
    generateFiles: (types: string[]) => Promise<void>;
}

export interface FieldTypeOption {
    value: FieldType;
    label: string;
    description: string;
    icon?: string;
    category: 'basic' | 'advanced' | 'special';
    hasOptions?: boolean;
    defaultOptions?: Partial<DoctypeFieldOptions>;
}

// Import/Export Types
export interface DoctypeExportData {
    doctype: Doctype;
    metadata: {
        version: string;
        exported_at: string;
        exported_by?: string;
    };
}

export interface DoctypeImportRequest {
    data: DoctypeExportData;
    options?: {
        overwrite?: boolean;
        update_existing?: boolean;
        generate_files?: boolean;
    };
}

export interface DoctypeImportResponse {
    success: boolean;
    data: {
        doctype: Doctype;
        conflicts?: string[];
        generated_files?: GeneratedFile[];
    };
    message?: string;
}

// Search and Filter Types
export interface SearchResult {
    id: number | string;
    doctype: string;
    title: string;
    description?: string;
    url?: string;
    highlight?: string;
    meta?: Record<string, any>;
}

export interface SearchRequest {
    query: string;
    doctypes?: string[];
    fields?: string[];
    limit?: number;
    filters?: Record<string, any>;
}

export interface SearchResponse {
    success: boolean;
    data: SearchResult[];
    total: number;
    query: string;
    took: number;
}

// Plugin and Extension Types
export interface DoctypePlugin {
    name: string;
    version: string;
    description: string;
    hooks?: DoctypeHooks;
    fieldTypes?: FieldTypeOption[];
    validators?: Record<string, Function>;
    formatters?: Record<string, Function>;
}

export interface DoctypeHooks {
    beforeSave?: (doctype: Doctype) => Promise<Doctype>;
    afterSave?: (doctype: Doctype) => Promise<void>;
    beforeDelete?: (doctype: Doctype) => Promise<boolean>;
    afterDelete?: (doctype: Doctype) => Promise<void>;
    beforeFieldRender?: (field: FormField, value: any) => Promise<FormField>;
    afterFieldRender?: (field: FormField, element: HTMLElement) => Promise<void>;
}
