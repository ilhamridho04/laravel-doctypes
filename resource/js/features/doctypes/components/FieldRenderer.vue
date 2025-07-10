<template>
    <div class="field-renderer">
        <!-- Text Input -->
        <div v-if="field.type === 'text'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" :type="field.type" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Textarea -->
        <div v-else-if="field.type === 'textarea'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <textarea :id="fieldId" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :rows="field.options?.rows || 3" :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Number Input -->
        <div v-else-if="field.type === 'number'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="number" :name="field.name" v-model.number="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :min="field.options?.min" :max="field.options?.max" :step="field.options?.step" :class="inputClasses"
                @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Email Input -->
        <div v-else-if="field.type === 'email'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="email" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Password Input -->
        <div v-else-if="field.type === 'password'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="password" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Select Dropdown -->
        <div v-else-if="field.type === 'select'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <select :id="fieldId" :name="field.name" v-model="localValue" :required="field.required"
                :disabled="readonly" :class="inputClasses" @change="handleInput" @blur="handleBlur">
                <option value="" disabled>{{ field.options?.placeholder || 'Select an option' }}</option>
                <option v-for="option in selectOptions" :key="typeof option === 'object' ? option.value : option"
                    :value="typeof option === 'object' ? option.value : option">
                    {{ typeof option === 'object' ? option.label : option }}
                </option>
            </select>
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Checkbox -->
        <div v-else-if="field.type === 'checkbox'" class="space-y-2">
            <div class="flex items-center space-x-2">
                <input :id="fieldId" type="checkbox" :name="field.name" v-model="localValue" :required="field.required"
                    :readonly="readonly" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    @change="handleInput" />
                <label v-if="field.label" :for="fieldId" class="text-sm font-medium text-gray-700">
                    {{ field.label }}
                    <span v-if="field.required" class="text-red-500">*</span>
                </label>
            </div>
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Date Input -->
        <div v-else-if="field.type === 'date'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="date" :name="field.name" v-model="localValue" :required="field.required"
                :readonly="readonly" :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- DateTime Input -->
        <div v-else-if="field.type === 'datetime'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="datetime-local" :name="field.name" v-model="localValue"
                :required="field.required" :readonly="readonly" :class="inputClasses" @input="handleInput"
                @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Time Input -->
        <div v-else-if="field.type === 'time'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="time" :name="field.name" v-model="localValue" :required="field.required"
                :readonly="readonly" :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- File Input -->
        <div v-else-if="field.type === 'file'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="file" :name="field.name" :required="field.required" :readonly="readonly"
                :accept="field.options?.accept" :class="inputClasses" @change="handleFileChange" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Image Input -->
        <div v-else-if="field.type === 'image'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <input :id="fieldId" type="file" :name="field.name" :required="field.required" :readonly="readonly"
                accept="image/*" :class="inputClasses" @change="handleFileChange" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- JSON Input -->
        <div v-else-if="field.type === 'json'" class="space-y-2">
            <label v-if="field.label" :for="fieldId" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <textarea :id="fieldId" :name="field.name" v-model="jsonValue"
                :placeholder="field.options?.placeholder || 'Enter valid JSON'" :required="field.required"
                :readonly="readonly" :rows="field.options?.rows || 5"
                :class="[inputClasses, jsonError ? 'border-red-500' : '']" @input="handleJsonInput"
                @blur="handleBlur" />
            <p v-if="jsonError" class="text-sm text-red-600">{{ jsonError }}</p>
            <p v-if="field.description" class="text-sm text-gray-500">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Unsupported field type -->
        <div v-else class="space-y-2">
            <label v-if="field.label" class="block text-sm font-medium text-gray-700">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-sm text-yellow-800">
                    Unsupported field type: <code class="font-mono">{{ field.type }}</code>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import type { DoctypeFormSchema } from '../types/doctype';

    interface Props {
        field: DoctypeFormSchema;
        modelValue: any;
        error?: string;
        readonly?: boolean;
    }

    interface Emits {
        (event: 'update:modelValue', value: any): void;
        (event: 'input', value: any): void;
        (event: 'blur', eventData: Event): void;
    }

    const props = withDefaults(defineProps<Props>(), {
        error: '',
        readonly: false,
    });

    const emit = defineEmits<Emits>();

    const localValue = ref(props.modelValue);
    const jsonValue = ref(JSON.stringify(props.modelValue, null, 2));
    const jsonError = ref('');

    // Watch for external changes to modelValue
    watch(() => props.modelValue, (newValue) => {
        localValue.value = newValue;
        if (props.field.type === 'json') {
            jsonValue.value = JSON.stringify(newValue, null, 2);
        }
    }, { deep: true });

    // Watch for internal changes to localValue
    watch(localValue, (newValue) => {
        emit('update:modelValue', newValue);
        emit('input', newValue);
    }, { deep: true });

    const fieldId = computed(() => `field-${props.field.name}`);

    const inputClasses = computed(() => [
        'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
        props.readonly ? 'bg-gray-50 text-gray-500' : 'bg-white',
        props.error ? 'border-red-500' : '',
    ]);

    const selectOptions = computed(() => {
        const options = props.field.options?.options || [];
        return Array.isArray(options) ? options : [];
    });

    const handleInput = (event: Event) => {
        const target = event.target as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement;
        localValue.value = target.value;
    };

    const handleBlur = (event: Event) => {
        emit('blur', event);
    };

    const handleFileChange = (event: Event) => {
        const target = event.target as HTMLInputElement;
        const file = target.files?.[0];
        localValue.value = file;
    };

    const handleJsonInput = (event: Event) => {
        const target = event.target as HTMLTextAreaElement;
        jsonValue.value = target.value;
        jsonError.value = '';

        try {
            const parsed = JSON.parse(target.value);
            localValue.value = parsed;
        } catch (error) {
            jsonError.value = 'Invalid JSON format';
        }
    };
</script>

<style scoped>
    .field-renderer {
        @apply w-full;
    }

    /* Custom styling for file inputs */
    input[type="file"] {
        @apply file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100;
    }

    /* JSON textarea styling */
    textarea[name*="json"] {
        @apply font-mono text-sm;
    }
</style>
