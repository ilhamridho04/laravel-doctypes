<template>
    <div class="field-renderer space-y-2 mb-4">
        <label class="block text-sm font-medium text-gray-700">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500 ml-1">*</span>
        </label>

        <p v-if="field.description" class="text-sm text-gray-500 mb-2">
            {{ field.description }}
        </p>

        <!-- Text Input -->
        <input v-if="field.type === 'text' || field.type === 'email' || field.type === 'password'" :type="field.type"
            :value="value" :disabled="disabled" :required="field.required" @input="updateValue($event.target.value)"
            :placeholder="field.label"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- Textarea -->
        <textarea v-else-if="field.type === 'textarea'" :value="value" :disabled="disabled" :required="field.required"
            @input="updateValue($event.target.value)" :rows="4" :placeholder="field.label"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"></textarea>

        <!-- Number Input -->
        <input v-else-if="field.type === 'number'" type="number" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue(Number($event.target.value))" :placeholder="field.label"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- Select -->
        <select v-else-if="field.type === 'select'" :disabled="disabled" :value="value"
            @change="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500">
            <option value="">Select {{ field.label }}</option>
            <option v-for="option in field.options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>

        <!-- Checkbox -->
        <div v-else-if="field.type === 'checkbox'" class="flex items-center space-x-2">
            <input type="checkbox" :id="field.name" :checked="value" :disabled="disabled"
                @change="updateValue($event.target.checked)"
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-50" />
            <label :for="field.name" class="text-sm font-normal text-gray-700">
                {{ field.label }}
            </label>
        </div>

        <!-- Date -->
        <input v-else-if="field.type === 'date'" type="date" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- DateTime -->
        <input v-else-if="field.type === 'datetime'" type="datetime-local" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- Time -->
        <input v-else-if="field.type === 'time'" type="time" :value="value" :disabled="disabled"
            :required="field.required" @input="updateValue($event.target.value)"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- File -->
        <input v-else-if="field.type === 'file' || field.type === 'image'" type="file" :disabled="disabled"
            :required="field.required" :accept="field.type === 'image' ? 'image/*' : '*'" @change="handleFileChange"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />

        <!-- JSON -->
        <textarea v-else-if="field.type === 'json'" :value="jsonValue" :disabled="disabled" :required="field.required"
            @input="updateJsonValue($event.target.value)" :rows="6" placeholder='{"key": "value"}'
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500 font-mono text-sm"></textarea>

        <!-- Fallback for unknown types -->
        <input v-else type="text" :value="value" :disabled="disabled" :required="field.required"
            @input="updateValue($event.target.value)" :placeholder="field.label"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500" />
    </div>
</template>

<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        field: {
            type: Object,
            required: true
        },
        value: {
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        }
    });

    const emit = defineEmits(['update:value']);

    const updateValue = (newValue) => {
        emit('update:value', newValue);
    };

    const jsonValue = computed(() => {
        if (props.field.type === 'json' && typeof props.value === 'object') {
            return JSON.stringify(props.value, null, 2);
        }
        return props.value || '';
    });

    const updateJsonValue = (jsonString) => {
        try {
            const parsed = JSON.parse(jsonString);
            emit('update:value', parsed);
        } catch (e) {
            // If JSON is invalid, emit the string value
            emit('update:value', jsonString);
        }
    };

    const handleFileChange = (event) => {
        const target = event.target;
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
