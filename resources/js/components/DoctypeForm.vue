<template>
    <div class="doctype-form">
        <Card>
            <CardHeader>
                <CardTitle>{{ doctype.title || doctype.name }}</CardTitle>
                <CardDescription v-if="doctype.description">
                    {{ doctype.description }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div v-for="field in fields" :key="field.fieldname" class="space-y-2">
                        <Label v-if="!field.hidden" :for="field.fieldname">
                            {{ field.label }}
                            <span v-if="field.reqd" class="text-destructive">*</span>
                        </Label>

                        <component :is="getFieldComponent(field.fieldtype)" :id="field.fieldname"
                            v-model="form[field.fieldname]" :field="field" :disabled="field.read_only"
                            :required="field.reqd" @update:modelValue="updateField(field.fieldname, $event)" />

                        <p v-if="field.description" class="text-sm text-muted-foreground">
                            {{ field.description }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <Button type="button" variant="outline" @click="$emit('cancel')">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="loading">
                            {{ loading ? 'Saving...' : 'Save' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
    import { ref, computed, onMounted } from 'vue'
    import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
    import { Button } from '@/components/ui/button'
    import { Label } from '@/components/ui/label'

    // Field components
    import DataField from './fields/DataField.vue'
    import SelectField from './fields/SelectField.vue'
    import CheckField from './fields/CheckField.vue'
    // ... other field imports

    interface Field {
        fieldname: string
        label: string
        fieldtype: string
        reqd?: boolean
        read_only?: boolean
        hidden?: boolean
        description?: string
        options?: string
        default?: any
    }

    interface Props {
        doctype: {
            name: string
            title?: string
            description?: string
            fields: Field[]
        }
        document?: any
    }

    const props = defineProps<Props>()
    const emit = defineEmits(['save', 'cancel'])

    const form = ref<Record<string, any>>({})
    const loading = ref(false)

    const fields = computed(() =>
        props.doctype.fields.filter(field => !field.hidden)
    )

    const getFieldComponent = (fieldtype: string) => {
        const componentMap = {
            'Data': DataField,
            'Select': SelectField,
            'Check': CheckField,
            // ... other mappings
        }
        return componentMap[fieldtype] || DataField
    }

    const updateField = (fieldname: string, value: any) => {
        form.value[fieldname] = value
    }

    const handleSubmit = async () => {
        loading.value = true
        try {
            emit('save', form.value)
        } finally {
            loading.value = false
        }
    }

    onMounted(() => {
        // Initialize form with document data or defaults
        props.doctype.fields.forEach(field => {
            form.value[field.fieldname] = props.document?.[field.fieldname] ?? field.default ?? null
        })
    })
</script>
