import { ref, computed } from 'vue';
import type {
    Doctype,
    DoctypeCreateRequest,
    DoctypeUpdateRequest,
    DoctypeListResponse,
    DoctypeResponse,
    DoctypeSchemaResponse,
    DoctypeFilters,
    DoctypeField
} from '../types/doctype';

export const useDoctypes = () => {
    const doctypes = ref<Doctype[]>([]);
    const currentDoctype = ref<Doctype | null>(null);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const meta = ref({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
    });

    const baseUrl = '/api/doctypes/doctypes';

    // Helper function to make API calls
    const apiCall = async <T>(
        url: string,
        options: RequestInit = {}
    ): Promise<T> => {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers,
            },
            ...options,
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return response.json();
    };

    // Get all doctypes with filters
    const fetchDoctypes = async (filters: DoctypeFilters = {}) => {
        loading.value = true;
        error.value = null;

        try {
            const params = new URLSearchParams();
            if (filters.search) params.append('search', filters.search);
            if (filters.active !== undefined) params.append('active', filters.active.toString());
            if (filters.system !== undefined) params.append('system', filters.system.toString());
            if (filters.per_page) params.append('per_page', filters.per_page.toString());
            if (filters.page) params.append('page', filters.page.toString());

            const response = await apiCall<DoctypeListResponse>(
                `${baseUrl}?${params.toString()}`
            );

            doctypes.value = response.data;
            meta.value = response.meta;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch doctypes';
        } finally {
            loading.value = false;
        }
    };

    // Get single doctype
    const fetchDoctype = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall<DoctypeResponse>(`${baseUrl}/${id}`);
            currentDoctype.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch doctype';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Create new doctype
    const createDoctype = async (data: DoctypeCreateRequest) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall<DoctypeResponse>(baseUrl, {
                method: 'POST',
                body: JSON.stringify(data),
            });

            doctypes.value.unshift(response.data);
            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to create doctype';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Update existing doctype
    const updateDoctype = async (id: number, data: DoctypeUpdateRequest) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall<DoctypeResponse>(`${baseUrl}/${id}`, {
                method: 'PUT',
                body: JSON.stringify(data),
            });

            const index = doctypes.value.findIndex(dt => dt.id === id);
            if (index !== -1) {
                doctypes.value[index] = response.data;
            }

            if (currentDoctype.value?.id === id) {
                currentDoctype.value = response.data;
            }

            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to update doctype';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Delete doctype
    const deleteDoctype = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            await apiCall(`${baseUrl}/${id}`, {
                method: 'DELETE',
            });

            doctypes.value = doctypes.value.filter(dt => dt.id !== id);

            if (currentDoctype.value?.id === id) {
                currentDoctype.value = null;
            }
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to delete doctype';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Get form schema for a doctype
    const getFormSchema = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall<DoctypeSchemaResponse>(`${baseUrl}/${id}/schema`);
            return response.schema;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch form schema';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Add field to doctype
    const addField = async (doctypeId: number, fieldData: Omit<DoctypeField, 'id'>) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall(`${baseUrl}/${doctypeId}/fields`, {
                method: 'POST',
                body: JSON.stringify(fieldData),
            });

            // Refresh the current doctype to include the new field
            if (currentDoctype.value?.id === doctypeId) {
                await fetchDoctype(doctypeId);
            }

            return response;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to add field';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Update field in doctype
    const updateField = async (doctypeId: number, fieldname: string, fieldData: Partial<DoctypeField>) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall(`${baseUrl}/${doctypeId}/fields/${fieldname}`, {
                method: 'PUT',
                body: JSON.stringify(fieldData),
            });

            // Refresh the current doctype to reflect changes
            if (currentDoctype.value?.id === doctypeId) {
                await fetchDoctype(doctypeId);
            }

            return response;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to update field';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Remove field from doctype
    const removeField = async (doctypeId: number, fieldname: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall(`${baseUrl}/${doctypeId}/fields/${fieldname}`, {
                method: 'DELETE',
            });

            // Refresh the current doctype to reflect changes
            if (currentDoctype.value?.id === doctypeId) {
                await fetchDoctype(doctypeId);
            }

            return response;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to remove field';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Clear current doctype
    const clearCurrentDoctype = () => {
        currentDoctype.value = null;
    };

    // Clear errors
    const clearError = () => {
        error.value = null;
    };

    // Computed properties
    const activeDoctypes = computed(() =>
        doctypes.value.filter(dt => dt.is_active)
    );

    const systemDoctypes = computed(() =>
        doctypes.value.filter(dt => dt.is_system)
    );

    const customDoctypes = computed(() =>
        doctypes.value.filter(dt => !dt.is_system)
    );

    const hasError = computed(() => error.value !== null);

    const isEmpty = computed(() => doctypes.value.length === 0);

    const totalPages = computed(() => meta.value.last_page);

    const currentPage = computed(() => meta.value.current_page);

    const totalItems = computed(() => meta.value.total);

    return {
        // State
        doctypes,
        currentDoctype,
        loading,
        error,
        meta,

        // Actions
        fetchDoctypes,
        fetchDoctype,
        createDoctype,
        updateDoctype,
        deleteDoctype,
        getFormSchema,
        addField,
        updateField,
        removeField,
        clearCurrentDoctype,
        clearError,

        // Computed
        activeDoctypes,
        systemDoctypes,
        customDoctypes,
        hasError,
        isEmpty,
        totalPages,
        currentPage,
        totalItems,
    };
};
