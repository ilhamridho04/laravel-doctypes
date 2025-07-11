import { ref } from 'vue';
import type {
    Doctype,
    DoctypeCreateRequest,
    DoctypeUpdateRequest,
    DoctypeListResponse,
    DoctypeResponse,
    DoctypeSchemaResponse,
    DoctypeGeneratorResponse,
    DoctypeFormSchema,
    FileGenerationRequest,
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

    // Fetch all doctypes
    const fetchDoctypes = async (page: number = 1, perPage: number = 15) => {
        loading.value = true;
        error.value = null;

        try {
            const url = `${baseUrl}?page=${page}&per_page=${perPage}`;
            const response: DoctypeListResponse = await apiCall(url);

            doctypes.value = response.data;
            meta.value = response.meta;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch doctypes';
            console.error('Error fetching doctypes:', err);
        } finally {
            loading.value = false;
        }
    };

    // Fetch single doctype
    const fetchDoctype = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response: DoctypeResponse = await apiCall(`${baseUrl}/${id}`);
            currentDoctype.value = response.data;
            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch doctype';
            console.error('Error fetching doctype:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Create doctype
    const createDoctype = async (data: DoctypeCreateRequest) => {
        loading.value = true;
        error.value = null;

        try {
            const response: DoctypeResponse = await apiCall(baseUrl, {
                method: 'POST',
                body: JSON.stringify(data),
            });

            // Add to local list
            doctypes.value.unshift(response.data);
            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to create doctype';
            console.error('Error creating doctype:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Update doctype
    const updateDoctype = async (id: number, data: DoctypeUpdateRequest) => {
        loading.value = true;
        error.value = null;

        try {
            const response: DoctypeResponse = await apiCall(`${baseUrl}/${id}`, {
                method: 'PUT',
                body: JSON.stringify(data),
            });

            // Update in local list
            const index = doctypes.value.findIndex(d => d.id === id);
            if (index !== -1) {
                doctypes.value[index] = response.data;
            }

            // Update current doctype if it's the one being edited
            if (currentDoctype.value?.id === id) {
                currentDoctype.value = response.data;
            }

            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to update doctype';
            console.error('Error updating doctype:', err);
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

            // Remove from local list
            doctypes.value = doctypes.value.filter(d => d.id !== id);

            // Clear current doctype if it's the one being deleted
            if (currentDoctype.value?.id === id) {
                currentDoctype.value = null;
            }
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to delete doctype';
            console.error('Error deleting doctype:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // Get form schema for a doctype
    const getFormSchema = async (doctypeName: string): Promise<DoctypeFormSchema> => {
        try {
            const response: DoctypeSchemaResponse = await apiCall(`${baseUrl}/${doctypeName}/schema`);
            return response.data;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to get form schema';
            console.error('Error getting form schema:', err);
            throw err;
        }
    };

    // Generate files for a doctype
    const generateFiles = async (request: FileGenerationRequest): Promise<string[]> => {
        loading.value = true;
        error.value = null;

        try {
            const response: DoctypeGeneratorResponse = await apiCall(`${baseUrl}/generate`, {
                method: 'POST',
                body: JSON.stringify(request),
            });

            return response.files;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to generate files';
            console.error('Error generating files:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

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
        generateFiles,
    };
};