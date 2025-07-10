<template>
    <div class="doctype-demo max-w-6xl mx-auto p-6 space-y-8">
        <!-- Header -->
        <div class="bg-card border border-border rounded-lg p-6">
            <h1 class="text-3xl font-bold text-foreground mb-2">DocType Form Builder Demo</h1>
            <p class="text-muted-foreground">
                This demo shows how to generate dynamic forms from DocType configurations and handle CRUD operations.
            </p>
        </div>

        <!-- DocType Selection -->
        <div class="bg-card border border-border rounded-lg p-6">
            <h2 class="text-xl font-semibold text-foreground mb-4">1. Select a DocType</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button v-for="doctype in availableDoctypes" :key="doctype.name" @click="selectDoctype(doctype.name)"
                    :class="[
                        'p-4 rounded-lg border text-left transition-colors',
                        selectedDoctypeName === doctype.name
                            ? 'border-primary bg-primary/10 text-primary'
                            : 'border-border hover:border-primary/50'
                    ]">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-semibold text-sm"
                            :style="{ backgroundColor: doctype.color || '#6b7280' }">
                            {{ doctype.icon || getInitials(doctype.label) }}
                        </div>
                        <div>
                            <h3 class="font-medium">{{ doctype.label }}</h3>
                            <p class="text-sm text-muted-foreground">{{ doctype.description }}</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Schema Preview -->
        <div v-if="currentSchema.length > 0" class="bg-card border border-border rounded-lg p-6">
            <h2 class="text-xl font-semibold text-foreground mb-4">2. Generated Form Schema</h2>
            <div class="bg-muted rounded-lg p-4 overflow-auto">
                <pre class="text-sm">{{ JSON.stringify(currentSchema, null, 2) }}</pre>
            </div>
        </div>

        <!-- Dynamic Form -->
        <div v-if="selectedDoctypeName && currentSchema.length > 0" class="bg-card border border-border rounded-lg p-6">
            <h2 class="text-xl font-semibold text-foreground mb-4">3. Dynamic Form</h2>

            <GeneratedForm :doctype-name="selectedDoctypeName" :record-id="editingRecordId" :show-debug="showDebugInfo"
                @submit="handleFormSubmit" @save="handleFormSave" @cancel="handleFormCancel" />
        </div>

        <!-- Generated Files Preview -->
        <div v-if="selectedDoctypeName" class="bg-card border border-border rounded-lg p-6">
            <h2 class="text-xl font-semibold text-foreground mb-4">4. Generate Laravel Files</h2>

            <div class="mb-4">
                <div class="flex flex-wrap gap-2 mb-4">
                    <label v-for="type in fileTypes" :key="type" class="flex items-center space-x-2">
                        <input type="checkbox" v-model="selectedFileTypes" :value="type" class="rounded border-input">
                        <span class="text-sm">{{ type }}</span>
                    </label>
                </div>

                <div class="flex space-x-2">
                    <button @click="previewGeneration" :disabled="generationLoading"
                        class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/80 disabled:opacity-50">
                        {{ generationLoading ? 'Loading...' : 'Preview' }}
                    </button>
                    <button @click="generateFiles" :disabled="generationLoading"
                        class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 disabled:opacity-50">
                        {{ generationLoading ? 'Generating...' : 'Generate Files' }}
                    </button>
                </div>
            </div>

            <div v-if="generationResults" class="space-y-4">
                <div v-for="(result, type) in generationResults" :key="type"
                    class="border border-border rounded-lg p-4">
                    <h4 class="font-semibold mb-2">{{ type }}</h4>
                    <div v-if="result.error" class="text-destructive text-sm">
                        Error: {{ result.error }}
                    </div>
                    <div v-else class="text-sm text-muted-foreground">
                        <p v-if="result.path">Path: {{ result.path }}</p>
                        <p v-if="result.exists !== undefined">
                            Status: {{ result.exists ? 'File exists' : 'Will be created' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records List -->
        <div v-if="selectedDoctypeName" class="bg-card border border-border rounded-lg p-6">
            <h2 class="text-xl font-semibold text-foreground mb-4">5. Generated Records</h2>

            <div v-if="recordsLoading" class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                <p class="mt-2 text-sm text-muted-foreground">Loading records...</p>
            </div>

            <div v-else-if="records.length === 0" class="text-center py-8 text-muted-foreground">
                No records found. Create one using the form above.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left p-2 font-medium">ID</th>
                            <th v-for="field in listViewFields" :key="field.name" class="text-left p-2 font-medium">
                                {{ field.label }}
                            </th>
                            <th class="text-left p-2 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="record in records" :key="record.id" class="border-b border-border hover:bg-muted/50">
                            <td class="p-2">{{ record.id }}</td>
                            <td v-for="field in listViewFields" :key="field.name" class="p-2">
                                {{ formatFieldValue(record[field.name], field.type) }}
                            </td>
                            <td class="p-2">
                                <div class="flex space-x-2">
                                    <button @click="editRecord(record.id)"
                                        class="px-2 py-1 text-xs bg-primary text-primary-foreground rounded hover:bg-primary/90">
                                        Edit
                                    </button>
                                    <button @click="deleteRecord(record.id)"
                                        class="px-2 py-1 text-xs bg-destructive text-destructive-foreground rounded hover:bg-destructive/90">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Debug Panel -->
        <div class="bg-card border border-border rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-foreground">Debug Panel</h2>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" v-model="showDebugInfo" class="rounded border-input">
                    <span class="text-sm">Show debug info in forms</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import GeneratedForm from './GeneratedForm.vue';
    import { useDoctypes } from '../services/useDoctypes';
    import type { Doctype, DoctypeFormSchema } from '../types/doctype';

    const {
        doctypes,
        loading,
        error,
        fetchDoctypes,
        fetchDoctypeSchema,
        generateDoctypeFiles
    } = useDoctypes();

    const selectedDoctypeName = ref<string>('');
    const currentSchema = ref<DoctypeFormSchema[]>([]);
    const editingRecordId = ref<string | number | undefined>();
    const showDebugInfo = ref(false);

    // File generation
    const fileTypes = ['model', 'controller', 'request', 'resource', 'migration'];
    const selectedFileTypes = ref<string[]>(['model', 'controller']);
    const generationLoading = ref(false);
    const generationResults = ref<Record<string, any> | null>(null);

    // Records management
    const records = ref<any[]>([]);
    const recordsLoading = ref(false);

    const availableDoctypes = computed(() => {
        return doctypes.value.filter(d => d.is_active);
    });

    const listViewFields = computed(() => {
        return currentSchema.value.filter(field =>
            // Show first few fields that would typically be in list view
            ['name', 'title', 'label', 'email', 'status', 'category'].includes(field.name) ||
            field.name.includes('name') ||
            field.name.includes('title')
        ).slice(0, 4);
    });

    const selectDoctype = async (doctypeName: string) => {
        selectedDoctypeName.value = doctypeName;
        editingRecordId.value = undefined;

        try {
            const response = await fetchDoctypeSchema(doctypeName);
            currentSchema.value = response.schema;
            await loadRecords();
        } catch (err) {
            console.error('Failed to load schema:', err);
            currentSchema.value = [];
        }
    };

    const loadRecords = async () => {
        if (!selectedDoctypeName.value) return;

        recordsLoading.value = true;
        try {
            const response = await fetch(`/api/${selectedDoctypeName.value}`);
            if (response.ok) {
                const data = await response.json();
                records.value = data.data || [];
            }
        } catch (err) {
            console.error('Failed to load records:', err);
            records.value = [];
        } finally {
            recordsLoading.value = false;
        }
    };

    const previewGeneration = async () => {
        if (!selectedDoctypeName.value) return;

        generationLoading.value = true;
        try {
            const results = await generateDoctypeFiles(selectedDoctypeName.value, {
                types: selectedFileTypes.value,
                preview: true
            });
            generationResults.value = results.generated;
        } catch (err) {
            console.error('Preview failed:', err);
        } finally {
            generationLoading.value = false;
        }
    };

    const generateFiles = async () => {
        if (!selectedDoctypeName.value) return;

        generationLoading.value = true;
        try {
            const results = await generateDoctypeFiles(selectedDoctypeName.value, {
                types: selectedFileTypes.value,
                force: true
            });
            generationResults.value = results.generated;
            alert('Files generated successfully!');
        } catch (err) {
            console.error('Generation failed:', err);
            alert('Generation failed. Check console for details.');
        } finally {
            generationLoading.value = false;
        }
    };

    const handleFormSubmit = async (data: any) => {
        console.log('Form submitted:', data);
        await loadRecords();
        alert('Record created successfully!');
    };

    const handleFormSave = async (data: any) => {
        console.log('Form saved:', data);
        await loadRecords();
        editingRecordId.value = undefined;
        alert('Record updated successfully!');
    };

    const handleFormCancel = () => {
        editingRecordId.value = undefined;
    };

    const editRecord = (id: string | number) => {
        editingRecordId.value = id;
    };

    const deleteRecord = async (id: string | number) => {
        if (!selectedDoctypeName.value) return;

        if (!confirm('Are you sure you want to delete this record?')) return;

        try {
            const response = await fetch(`/api/${selectedDoctypeName.value}/${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                await loadRecords();
                alert('Record deleted successfully!');
            }
        } catch (err) {
            console.error('Delete failed:', err);
            alert('Delete failed. Check console for details.');
        }
    };

    const formatFieldValue = (value: any, type: string): string => {
        if (value === null || value === undefined) return '-';

        switch (type) {
            case 'checkbox':
                return value ? 'Yes' : 'No';
            case 'date':
            case 'datetime':
                return new Date(value).toLocaleDateString();
            case 'json':
                return JSON.stringify(value);
            default:
                return String(value);
        }
    };

    const getInitials = (name: string): string => {
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .slice(0, 2);
    };

    onMounted(() => {
        fetchDoctypes();
    });
</script>

<style scoped>
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    pre {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
</style>
