<template>
    <div class="doctype-form max-w-4xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-foreground">
                {{ isEditing ? 'Edit DocType' : 'Create DocType' }}
            </h1>
            <p class="mt-2 text-muted-foreground">
                {{ isEditing ? 'Update your doctype configuration' : 'Define a new document type with custom fields' }}
            </p>
        </div>

        <form @submit.prevent="submitForm" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-card rounded-lg border border-border p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-foreground mb-6">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Name <span class="text-destructive">*</span>
                        </label>
                        <input id="name" type="text" v-model="formData.name" :disabled="isEditing" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="user_profile" />
                        <p class="text-sm text-muted-foreground">
                            Unique identifier (letters, numbers, underscores only)
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="label"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Label <span class="text-destructive">*</span>
                        </label>
                        <input id="label" type="text" v-model="formData.label" required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="User Profile" />
                        <p class="text-sm text-muted-foreground">
                            Display name for the doctype
                        </p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label for="description"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Description
                        </label>
                        <textarea id="description" v-model="formData.description" rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Brief description of what this doctype represents" />
                    </div>

                    <div class="space-y-2">
                        <label for="icon"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Icon
                        </label>
                        <input id="icon" type="text" v-model="formData.icon"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="user" />
                        <p class="text-sm text-muted-foreground">
                            Icon identifier (e.g., user, document, settings)
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="color"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Color
                        </label>
                        <div class="flex items-center space-x-2">
                            <input id="color" type="color" v-model="formData.color"
                                class="h-10 w-16 rounded-md border border-input cursor-pointer" />
                            <input type="text" v-model="formData.color"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="#3b82f6" />
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex items-center space-x-2">
                            <input id="is_active" type="checkbox" v-model="formData.is_active"
                                class="h-4 w-4 rounded border border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2" />
                            <label for="is_active"
                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fields Configuration -->
            <div class="bg-card rounded-lg border border-border p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-foreground">Fields</h2>
                    <button type="button" @click="addField"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Add Field
                    </button>
                </div>

                <div v-if="formData.fields.length === 0" class="text-center py-12">
                    <DocumentIcon class="mx-auto h-12 w-12 text-muted-foreground mb-4" />
                    <h3 class="text-lg font-medium text-foreground mb-2">No fields yet</h3>
                    <p class="text-muted-foreground mb-4">Start building your form by adding fields</p>
                    <button type="button" @click="addField"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        <PlusIcon class="w-4 h-4 mr-2" />
                        Add Your First Field
                    </button>
                </div>

                <div v-else class="space-y-6">
                    <div v-for="(field, index) in formData.fields" :key="field.fieldname || index"
                        class="bg-muted/50 rounded-lg border border-border p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-foreground">
                                Field {{ index + 1 }}: {{ field.label || 'Untitled Field' }}
                            </h3>
                            <button type="button" @click="removeField(index)"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive hover:text-destructive"
                                title="Remove field">
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label :for="`field-${index}-name`"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Field Name <span class="text-destructive">*</span>
                                </label>
                                <input :id="`field-${index}-name`" type="text" v-model="field.fieldname" required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="first_name" />
                            </div>

                            <div class="space-y-2">
                                <label :for="`field-${index}-label`"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Label <span class="text-destructive">*</span>
                                </label>
                                <input :id="`field-${index}-label`" type="text" v-model="field.label" required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="First Name" />
                            </div>

                            <div class="space-y-2">
                                <label :for="`field-${index}-type`"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Field Type <span class="text-destructive">*</span>
                                </label>
                                <select :id="`field-${index}-type`" v-model="field.fieldtype" required
                                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">Select type</option>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="email">Email</option>
                                    <option value="password">Password</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="date">Date</option>
                                    <option value="datetime">DateTime</option>
                                    <option value="time">Time</option>
                                    <option value="file">File</option>
                                    <option value="image">Image</option>
                                    <option value="json">JSON</option>
                                </select>
                            </div>

                            <div class="md:col-span-2 lg:col-span-3 space-y-2">
                                <label :for="`field-${index}-description`"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Description
                                </label>
                                <input :id="`field-${index}-description`" type="text" v-model="field.description"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Brief description of this field" />
                            </div>

                            <!-- Select Options -->
                            <div v-if="field.fieldtype === 'select'" class="md:col-span-2 lg:col-span-3 space-y-2">
                                <label
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                    Select Options
                                </label>
                                <div class="space-y-3">
                                    <div v-for="(option, optIndex) in getSelectOptions(field)" :key="optIndex"
                                        class="flex items-center space-x-2">
                                        <input type="text" v-model="getSelectOptions(field)[optIndex]"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Option value" />
                                        <button type="button" @click="removeSelectOption(field, optIndex)"
                                            class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive hover:text-destructive">
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                    <button type="button" @click="addSelectOption(field)"
                                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                                        <PlusIcon class="w-4 h-4 mr-2" />
                                        Add Option
                                    </button>
                                </div>
                            </div>

                            <!-- Field Options -->
                            <div class="flex flex-wrap gap-4 md:col-span-2 lg:col-span-3">
                                <div class="flex items-center space-x-2">
                                    <input :id="`field-${index}-required`" type="checkbox" v-model="field.required"
                                        class="h-4 w-4 rounded border border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2" />
                                    <label :for="`field-${index}-required`"
                                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Required
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input :id="`field-${index}-unique`" type="checkbox" v-model="field.unique"
                                        class="h-4 w-4 rounded border border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2" />
                                    <label :for="`field-${index}-unique`"
                                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Unique
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input :id="`field-${index}-list`" type="checkbox" v-model="field.in_list_view"
                                        class="h-4 w-4 rounded border border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2" />
                                    <label :for="`field-${index}-list`"
                                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Show in List
                                    </label>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input :id="`field-${index}-filter`" type="checkbox"
                                        v-model="field.in_standard_filter"
                                        class="h-4 w-4 rounded border border-input text-primary focus:ring-2 focus:ring-ring focus:ring-offset-2" />
                                    <label :for="`field-${index}-filter`"
                                        class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                        Filterable
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6">
                <button type="button" @click="$emit('cancel')"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    Cancel
                </button>
                <button type="submit" :disabled="loading"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    {{ loading ? 'Saving...' : (isEditing ? 'Update DocType' : 'Create DocType') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue';
    import { useDoctypes } from '../services/useDoctypes';
    import type { Doctype, DoctypeField } from '../types/doctype';
    import { PlusIcon, TrashIcon, DocumentIcon } from '@heroicons/vue/24/outline';

    interface Props {
        doctype?: Doctype;
    }

    interface Emits {
        (event: 'saved', doctype: Doctype): void;
        (event: 'cancel'): void;
    }

    const props = defineProps<Props>();
    const emit = defineEmits<Emits>();

    const { createDoctype, updateDoctype, loading } = useDoctypes();

    const isEditing = computed(() => !!props.doctype?.id);

    const formData = ref<{
        name: string;
        label: string;
        description: string;
        icon: string;
        color: string;
        is_active: boolean;
        fields: DoctypeField[];
    }>({
        name: '',
        label: '',
        description: '',
        icon: '',
        color: '#3b82f6',
        is_active: true,
        fields: [],
    });

    const addField = () => {
        formData.value.fields.push({
            fieldname: '',
            label: '',
            fieldtype: 'text',
            required: false,
            unique: false,
            in_list_view: false,
            in_standard_filter: false,
            description: '',
            sort_order: formData.value.fields.length,
        });
    };

    const removeField = (index: number) => {
        formData.value.fields.splice(index, 1);
        // Update sort_order for remaining fields
        formData.value.fields.forEach((field, idx) => {
            field.sort_order = idx;
        });
    };

    const getSelectOptions = (field: DoctypeField): string[] => {
        if (!field.options) {
            field.options = { options: [] };
        }
        if (!field.options.options) {
            field.options.options = [];
        }
        return field.options.options as string[];
    };

    const addSelectOption = (field: DoctypeField) => {
        const options = getSelectOptions(field);
        options.push('');
    };

    const removeSelectOption = (field: DoctypeField, index: number) => {
        const options = getSelectOptions(field);
        options.splice(index, 1);
    };

    const submitForm = async () => {
        try {
            let result: Doctype;

            if (isEditing.value && props.doctype?.id) {
                result = await updateDoctype(props.doctype.id, formData.value);
            } else {
                result = await createDoctype(formData.value);
            }

            emit('saved', result);
        } catch (error) {
            console.error('Failed to save doctype:', error);
        }
    };

    onMounted(() => {
        if (props.doctype) {
            formData.value = {
                name: props.doctype.name,
                label: props.doctype.label,
                description: props.doctype.description || '',
                icon: props.doctype.icon || '',
                color: props.doctype.color || '#3b82f6',
                is_active: props.doctype.is_active ?? true,
                fields: props.doctype.fields || [],
            };
        }
    });
</script>
