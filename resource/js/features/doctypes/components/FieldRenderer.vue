<template>
    <div class="field-renderer">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
        </label>

        <p v-if="field.description" class="text-sm text-gray-500 mb-2">
            {{ field.description }}
        </p>

        <!-- Text Input -->
        <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'" :type="field.type"
            :value="value" :disabled="disabled" :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :placeholder="field.label" />

        <!-- Textarea -->
        <textarea v-else-if="field.type === 'textarea'" :value="value" :disabled="disabled" :required="field.required"
            @input="updateValue($event.target.value)" rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :placeholder="field.label"></textarea>

        <!-- Number Input -->
        <input v-else-if="field.type === 'number'" type="number" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue(Number($event.target.value))"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :placeholder="field.label" />

        <!-- Select -->
        <select v-else-if="field.type === 'select'" :value="value" :disabled="disabled" :required="field.required"
            @change="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select {{ field.label }}</option>
            <option v-for="option in field.options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>

        <!-- Checkbox -->
        <label v-else-if="field.type === 'checkbox'" class="flex items-center">
            <input type="checkbox" :checked="value" :disabled="disabled" @change="updateValue($event.target.checked)"
                class="mr-2" />
            <span>{{ field.label }}</span>
        </label>

        <!-- Date -->
        <input v-else-if="field.type === 'date'" type="date" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- DateTime -->
        <input v-else-if="field.type === 'datetime'" type="datetime-local" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- Time -->
        <input v-else-if="field.type === 'time'" type="time" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- File -->
        <input v-else-if="field.type === 'file' || field.type === 'image'"
            :type="field.type === 'image' ? 'file' : 'file'" :disabled="disabled" :required="field.required"
            :accept="field.type === 'image' ? 'image/*' : '*'" @change="handleFileChange"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <!-- JSON -->
        <textarea v-else-if="field.type === 'json'" :value="jsonValue" :disabled="disabled" :required="field.required"
            @input="updateJsonValue($event.target.value)" rows="6"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
            placeholder='{"key": "value"}'></textarea>

        <!-- Fallback for unknown types -->
        <input v-else type="text" :value="value" :disabled="disabled" :required="field.required"
            @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            :placeholder="field.label" />
    </div>
</template>

<script setup lang="ts">
    import { computed } from 'vue';
    import type { FormField } from '../types/doctype';

    interface Props {
        field: FormField;
        value: any;
        disabled?: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {
        disabled: false
    });

    const emit = defineEmits<{
        'update:value': [value: any];
    }>();

    const updateValue = (newValue: any) => {
        emit('update:value', newValue);
    };

    const jsonValue = computed(() => {
        if (props.field.type === 'json' && typeof props.value === 'object') {
            return JSON.stringify(props.value, null, 2);
        }
        return props.value || '';
    });

    const updateJsonValue = (jsonString: string) => {
        try {
            const parsed = JSON.parse(jsonString);
            emit('update:value', parsed);
        } catch (e) {
            // If JSON is invalid, emit the string value
            emit('update:value', jsonString);
        }
    };

    const handleFileChange = (event: Event) => {
        const target = event.target as HTMLInputElement;
        const file = target.files?.[0];
        if (file) {
            emit('update:value', file);
        }
    };
</script>

<style scoped>
    .field-renderer {
        margin-bottom: 1rem;
    }
</style>
