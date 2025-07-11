<template>
    <div class="generated-form">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold">{{ schema?.title || doctypeName }}</h1>
                <p v-if="schema?.description" class="text-gray-600 mt-1">
                    {{ schema.description }}
                </p>
            </div>
            <button v-if="mode !== 'view'" @click="handleSubmit" :disabled="loading" class="btn btn-primary">
                {{ loading ? 'Saving...' : (mode === 'edit' ? 'Update' : 'Create') }}
            </button>
        </div>

        <div v-if="loading && !schema" class="text-center py-8">
            Loading form schema...
        </div>

        <div v-else-if="error" class="alert alert-error mb-6">
            {{ error }}
        </div>

        <form v-else-if="schema" @submit.prevent="handleSubmit" class="space-y-6">
            <div v-for="field in schema.fields" :key="field.name" class="bg-white p-4 rounded-lg shadow">
                <FieldRenderer :field="field" :value="formData[field.name]" :disabled="mode === 'view'"
                    @update:value="updateField(field.name, $event)" />
            </div>

            <div v-if="mode !== 'view'" class="flex justify-end space-x-4">
                <button type="button" @click="resetForm" class="btn btn-secondary">
                    Reset
                </button>
                <button type="submit" :disabled="loading" class="btn btn-primary">
                    {{ loading ? 'Saving...' : (mode === 'edit' ? 'Update' : 'Create') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, onMounted, computed } from 'vue';
    import { useRoute } from 'vue-router';
    import { useDoctypes } from '../services/useDoctypes';
    import FieldRenderer from '../components/FieldRenderer.vue';
    import type { DoctypeFormSchema } from '../types/doctype';

    interface Props {
        doctypeName: string;
        recordId?: number;
        mode?: 'create' | 'edit' | 'view';
        initialData?: Record<string, any>;
    }

    const props = withDefaults(defineProps<Props>(), {
        mode: 'create'
    });

    const emit = defineEmits<{
        save: [data: Record<string, any>];
        cancel: [];
    }>();

    const route = useRoute();
    const { getFormSchema, loading, error } = useDoctypes();

    const schema = ref<DoctypeFormSchema | null>(null);
    const formData = ref<Record<string, any>>({});

    const updateField = (fieldName: string, value: any) => {
        formData.value[fieldName] = value;
    };

    const resetForm = () => {
        if (schema.value) {
            const newData: Record<string, any> = {};
            schema.value.fields.forEach(field => {
                newData[field.name] = field.default_value ?? '';
            });
            formData.value = { ...newData, ...props.initialData };
        }
    };

    const handleSubmit = () => {
        emit('save', { ...formData.value });
    };

    onMounted(async () => {
        try {
            schema.value = await getFormSchema(props.doctypeName);
            resetForm();
        } catch (err) {
            console.error('Error loading form schema:', err);
        }
    });
</script>

<style scoped>
    .btn {
        @apply px-4 py-2 rounded-md font-medium transition-colors;
    }

    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700;
    }

    .btn-secondary {
        @apply bg-gray-300 text-gray-700 hover:bg-gray-400;
    }

    .btn:disabled {
        @apply opacity-50 cursor-not-allowed;
    }

    .alert {
        @apply p-4 rounded-md;
    }

    .alert-error {
        @apply bg-red-100 border border-red-400 text-red-700;
    }
</style>
