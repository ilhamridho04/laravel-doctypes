<template>
    <div class="field-renderer w-full">
        <!-- Text Input -->
        <div v-if="field.type === 'text'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" :type="field.type" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Textarea -->
        <div v-else-if="field.type === 'textarea'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <textarea :id="fieldId" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :rows="field.options?.rows || 3" :class="textareaClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Number Input -->
        <div v-else-if="field.type === 'number'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="number" :name="field.name" v-model.number="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :min="field.options?.min" :max="field.options?.max" :step="field.options?.step" :class="inputClasses"
                @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Email Input -->
        <div v-else-if="field.type === 'email'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="email" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Password Input -->
        <div v-else-if="field.type === 'password'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="password" :name="field.name" v-model="localValue"
                :placeholder="field.options?.placeholder || ''" :required="field.required" :readonly="readonly"
                :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Select Dropdown -->
        <div v-else-if="field.type === 'select'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <select :id="fieldId" :name="field.name" v-model="localValue" :required="field.required"
                :disabled="readonly" :class="selectClasses" @change="handleInput" @blur="handleBlur">
                <option value="" disabled>{{ field.options?.placeholder || 'Select an option' }}</option>
                <option v-for="option in selectOptions" :key="typeof option === 'object' ? option.value : option"
                    :value="typeof option === 'object' ? option.value : option">
                    {{ typeof option === 'object' ? option.label : option }}
                </option>
            </select>
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Checkbox -->
        <div v-else-if="field.type === 'checkbox'" class="space-y-2">
            <div class="flex items-center space-x-2">
                <input :id="fieldId" type="checkbox" :name="field.name" v-model="localValue" :required="field.required"
                    :readonly="readonly" :class="checkboxClasses" @change="handleInput" />
                <label v-if="field.label" :for="fieldId"
                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    {{ field.label }}
                    <span v-if="field.required" class="text-destructive">*</span>
                </label>
            </div>
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Date Input -->
        <div v-else-if="field.type === 'date'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="date" :name="field.name" v-model="localValue" :required="field.required"
                :readonly="readonly" :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- DateTime Input -->
        <div v-else-if="field.type === 'datetime'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="datetime-local" :name="field.name" v-model="localValue"
                :required="field.required" :readonly="readonly" :class="inputClasses" @input="handleInput"
                @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Time Input -->
        <div v-else-if="field.type === 'time'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="time" :name="field.name" v-model="localValue" :required="field.required"
                :readonly="readonly" :class="inputClasses" @input="handleInput" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- File Input -->
        <div v-else-if="field.type === 'file'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="file" :name="field.name" :required="field.required" :readonly="readonly"
                :accept="field.options?.accept" :class="fileInputClasses" @change="handleFileChange"
                @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Image Input -->
        <div v-else-if="field.type === 'image'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <input :id="fieldId" type="file" :name="field.name" :required="field.required" :readonly="readonly"
                accept="image/*" :class="fileInputClasses" @change="handleFileChange" @blur="handleBlur" />
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- JSON Input -->
        <div v-else-if="field.type === 'json'" class="space-y-2">
            <label v-if="field.label" :for="fieldId"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <textarea :id="fieldId" :name="field.name" v-model="jsonValue"
                :placeholder="field.options?.placeholder || 'Enter valid JSON'" :required="field.required"
                :readonly="readonly" :rows="field.options?.rows || 5"
                :class="[textareaClasses, jsonError ? 'border-destructive' : '']" @input="handleJsonInput"
                @blur="handleBlur" />
            <p v-if="jsonError" class="text-sm text-destructive">{{ jsonError }}</p>
            <p v-if="field.description" class="text-sm text-muted-foreground">{{ field.description }}</p>
            <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        </div>

        <!-- Unsupported field type -->
        <div v-else class="space-y-2">
            <label v-if="field.label"
                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                {{ field.label }}
                <span v-if="field.required" class="text-destructive">*</span>
            </label>
            <div class="p-3 bg-warning/10 border border-warning rounded-md">
                <p class="text-sm text-warning-foreground">
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
        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        props.readonly ? 'opacity-50 cursor-not-allowed' : '',
        props.error ? 'border-destructive focus-visible:ring-destructive' : '',
    ]);

    const textareaClasses = computed(() => [
        'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        props.readonly ? 'opacity-50 cursor-not-allowed' : '',
        props.error ? 'border-destructive focus-visible:ring-destructive' : '',
    ]);

    const selectClasses = computed(() => [
        'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        props.readonly ? 'opacity-50 cursor-not-allowed' : '',
        props.error ? 'border-destructive focus:ring-destructive' : '',
    ]);

    const checkboxClasses = computed(() => [
        'peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground',
        props.error ? 'border-destructive' : '',
    ]);

    const fileInputClasses = computed(() => [
        'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
        props.readonly ? 'opacity-50 cursor-not-allowed' : '',
        props.error ? 'border-destructive focus-visible:ring-destructive' : '',
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
        width: 100%;
    }

    /* JSON textarea styling - using standard CSS for Tailwind v4 compatibility */
    textarea[name*="json"] {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
    }
</style>
