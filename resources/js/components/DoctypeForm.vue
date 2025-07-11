<template>
    <div class="doctype-form">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ doctype.title || doctype.name }}</h3>
                <p v-if="doctype.description" class="text-sm text-gray-500 mt-1">
                    {{ doctype.description }}
                </p>
            </div>
            <div class="p-6">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div v-for="field in fields" :key="field.fieldname" class="space-y-2">
                        <label v-if="!field.hidden" :for="field.fieldname"
                            class="block text-sm font-medium text-gray-700">
                            {{ field.label }}
                            <span v-if="field.reqd" class="text-red-500 ml-1">*</span>
                        </label>

                        <component :is="getFieldComponent(field.fieldtype)" :id="field.fieldname"
                            v-model="form[field.fieldname]" :field="field" :disabled="field.read_only"
                            :required="field.reqd" @update:modelValue="updateField(field.fieldname, $event)" />

                        <p v-if="field.description" class="text-sm text-gray-500">
                            {{ field.description }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" @click="$emit('cancel')"
                            class="bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ loading ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';

    // Simple field components or fallback to input
    const DataField = 'input';
    const SelectField = 'select';
    const CheckField = 'input';

    const props = defineProps({
        doctype: {
            type: Object,
            required: true
        },
        document: {
            type: Object,
            default: null
        }
    });

    const emit = defineEmits(['save', 'cancel']);

    const form = ref({});
    const loading = ref(false);

    const fields = computed(() =>
        props.doctype.fields ? props.doctype.fields.filter(field => !field.hidden) : []
    );

    const getFieldComponent = (fieldtype) => {
        const componentMap = {
            'Data': DataField,
            'Select': SelectField,
            'Check': CheckField,
            'text': 'input',
            'select': 'select',
            'checkbox': 'input',
            'textarea': 'textarea'
        };
        return componentMap[fieldtype] || 'input';
    };

    const updateField = (fieldname, value) => {
        form.value[fieldname] = value;
    };

    const handleSubmit = async () => {
        loading.value = true;
        try {
            emit('save', form.value);
        } finally {
            loading.value = false;
        }
    };

    onMounted(() => {
        // Initialize form with document data or defaults
        if (props.doctype.fields) {
            props.doctype.fields.forEach(field => {
                form.value[field.fieldname] = props.document?.[field.fieldname] ?? field.default ?? null;
            });
        }
    });
</script>
