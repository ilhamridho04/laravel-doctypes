<template>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ doctype?.title || doctype?.name || 'Create Document' }}
            </h3>
            <p v-if="doctype?.description" class="text-sm text-gray-600 mt-1">
                {{ doctype.description }}
            </p>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
            <div v-for="field in visibleFields" :key="field.fieldname" class="space-y-2">
                <label :for="field.fieldname" class="block text-sm font-medium text-gray-700">
                    {{ field.label }}
                    <span v-if="field.reqd" class="text-red-500 ml-1">*</span>
                </label>

                <!-- Text/Data Field -->
                <input v-if="['Data', 'text', 'email', 'password'].includes(field.fieldtype)" :id="field.fieldname"
                    v-model="formData[field.fieldname]" :type="getInputType(field.fieldtype)" :required="field.reqd"
                    :disabled="field.read_only" :placeholder="field.placeholder"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />

                <!-- Textarea -->
                <textarea v-else-if="['Small Text', 'Long Text', 'textarea'].includes(field.fieldtype)"
                    :id="field.fieldname" v-model="formData[field.fieldname]" :required="field.reqd"
                    :disabled="field.read_only" :placeholder="field.placeholder" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"></textarea>

                <!-- Select -->
                <select v-else-if="['Select', 'select'].includes(field.fieldtype)" :id="field.fieldname"
                    v-model="formData[field.fieldname]" :required="field.reqd" :disabled="field.read_only"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                    <option value="">Select {{ field.label }}</option>
                    <option v-for="option in getSelectOptions(field)" :key="option" :value="option">
                        {{ option }}
                    </option>
                </select>

                <!-- Checkbox -->
                <div v-else-if="['Check', 'checkbox'].includes(field.fieldtype)" class="flex items-center">
                    <input :id="field.fieldname" v-model="formData[field.fieldname]" type="checkbox"
                        :disabled="field.read_only"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                    <label :for="field.fieldname" class="ml-2 text-sm text-gray-700">
                        {{ field.label }}
                    </label>
                </div>

                <!-- Number -->
                <input v-else-if="['Int', 'Float', 'number'].includes(field.fieldtype)" :id="field.fieldname"
                    v-model.number="formData[field.fieldname]" type="number" :required="field.reqd"
                    :disabled="field.read_only" :placeholder="field.placeholder"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />

                <!-- Date -->
                <input v-else-if="['Date', 'date'].includes(field.fieldtype)" :id="field.fieldname"
                    v-model="formData[field.fieldname]" type="date" :required="field.reqd" :disabled="field.read_only"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />

                <!-- Fallback for unknown field types -->
                <input v-else :id="field.fieldname" v-model="formData[field.fieldname]" type="text"
                    :required="field.reqd" :disabled="field.read_only" :placeholder="field.placeholder"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />

                <p v-if="field.description" class="text-sm text-gray-500">
                    {{ field.description }}
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" @click="$emit('cancel')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="loading"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    {{ loading ? 'Saving...' : 'Save Document' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import { ref, computed, onMounted, watch } from 'vue'

    export default {
        name: 'DoctypeForm',
        props: {
            doctype: {
                type: Object,
                required: true
            },
            document: {
                type: Object,
                default: null
            }
        },
        emits: ['save', 'cancel'],
        setup(props, { emit }) {
            const formData = ref({})
            const loading = ref(false)

            const visibleFields = computed(() => {
                if (!props.doctype?.fields) return []
                return props.doctype.fields.filter(field => !field.hidden)
            })

            const getInputType = (fieldtype) => {
                const typeMap = {
                    'Data': 'text',
                    'text': 'text',
                    'email': 'email',
                    'password': 'password',
                    'Int': 'number',
                    'Float': 'number',
                    'number': 'number',
                    'Date': 'date',
                    'date': 'date'
                }
                return typeMap[fieldtype] || 'text'
            }

            const getSelectOptions = (field) => {
                if (field.options) {
                    return field.options.split('\n').filter(option => option.trim())
                }
                return []
            }

            const initializeForm = () => {
                if (!props.doctype?.fields) return

                const initialData = {}
                props.doctype.fields.forEach(field => {
                    if (props.document && props.document[field.fieldname] !== undefined) {
                        initialData[field.fieldname] = props.document[field.fieldname]
                    } else if (field.default !== undefined) {
                        initialData[field.fieldname] = field.default
                    } else {
                        initialData[field.fieldname] = field.fieldtype === 'Check' ? false : ''
                    }
                })
                formData.value = initialData
            }

            const handleSubmit = async () => {
                loading.value = true
                try {
                    emit('save', { ...formData.value })
                } catch (error) {
                    console.error('Error saving form:', error)
                } finally {
                    loading.value = false
                }
            }

            // Watch for doctype changes and reinitialize form
            watch(() => props.doctype, initializeForm, { immediate: true })

            onMounted(() => {
                initializeForm()
            })

            return {
                formData,
                loading,
                visibleFields,
                getInputType,
                getSelectOptions,
                handleSubmit
            }
        }
    }
</script>
