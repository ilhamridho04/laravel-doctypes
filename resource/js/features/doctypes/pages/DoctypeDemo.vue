<template>
    <div class="doctype-demo max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">DocType Demo</h1>
            <p class="text-gray-600 mt-2">Explore and test DocType functionality with live examples</p>
        </div>

        <!-- Demo Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- DocType Creation Demo -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Create DocType</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Create a new DocType with custom fields and configurations
                    </p>
                </div>
                <div class="p-6">
                    <router-link to="/doctypes/create">
                        <button
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-flex items-center justify-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create New DocType
                        </button>
                    </router-link>
                </div>
            </div>

            <!-- DocType List Demo -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Browse DocTypes</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        View and manage existing DocTypes in your system
                    </p>
                </div>
                <div class="p-6">
                    <router-link to="/doctypes">
                        <button
                            class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-300 transition-colors inline-flex items-center justify-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            View All DocTypes
                        </button>
                    </router-link>
                </div>
            </div>

            <!-- Sample DocType Demo -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sample Forms</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Try out pre-built DocType forms with different field types
                    </p>
                </div>
                <div class="p-6 space-y-3">
                    <button v-for="sampleType in sampleDocTypes" :key="sampleType.name"
                        @click="loadSampleDocType(sampleType.name)"
                        class="w-full bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-md border border-gray-200 transition-colors text-left inline-flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        {{ sampleType.label }}
                    </button>
                </div>
            </div>

            <!-- API Demo -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">API Testing</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Test DocType API endpoints and view responses
                    </p>
                </div>
                <div class="p-6">
                    <button @click="showApiDemo = true"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition-colors inline-flex items-center justify-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        API Documentation
                    </button>
                </div>
            </div>
        </div>

        <!-- Live Form Preview -->
        <div v-if="selectedDocType" class="bg-white shadow rounded-lg mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Live Preview: {{ selectedDocType.title }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    This is how your DocType form will look to users
                </p>
            </div>
            <div class="p-6">
                <GeneratedForm :doctype-name="selectedDocType.name" mode="create" @save="handleFormSave"
                    @cancel="selectedDocType = null" />
            </div>
        </div>

        <!-- API Demo Modal -->
        <div v-if="showApiDemo" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showApiDemo = false"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">DocType API Reference</h3>
                                <p class="text-sm text-gray-500 mt-1">Available API endpoints for DocType operations</p>
                            </div>
                            <button @click="showApiDemo = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <div v-for="endpoint in apiEndpoints" :key="endpoint.method + endpoint.path"
                                class="border rounded-lg p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span :class="getMethodClass(endpoint.method)"
                                        class="px-2 py-1 text-xs font-medium rounded">
                                        {{ endpoint.method }}
                                    </span>
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ endpoint.path }}</code>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ endpoint.description }}</p>
                                <details class="text-sm">
                                    <summary class="cursor-pointer text-blue-600 hover:text-blue-800">View Example
                                    </summary>
                                    <pre
                                        class="mt-2 p-2 bg-gray-100 rounded text-xs overflow-x-auto">{{ endpoint.example }}</pre>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="message" :class="getAlertClass(messageType)" class="mt-6 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg v-if="messageType === 'default'" class="h-5 w-5 text-green-400" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg v-else class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p :class="getMessageTextClass(messageType)" class="text-sm font-medium">
                        {{ message }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';

    // Components
    import GeneratedForm from './GeneratedForm.vue';

    // State
    const selectedDocType = ref(null);
    const showApiDemo = ref(false);
    const message = ref('');
    const messageType = ref('default');

    // Sample DocTypes with simple SVG icons
    const sampleDocTypes = [
        {
            name: 'user',
            label: 'User Profile',
            icon: 'svg',
            title: 'User Profile Form'
        },
        {
            name: 'order',
            label: 'Order Form',
            icon: 'svg',
            title: 'Order Management'
        },
        {
            name: 'document',
            label: 'Document',
            icon: 'svg',
            title: 'Document Management'
        },
        {
            name: 'settings',
            label: 'Settings',
            icon: 'svg',
            title: 'Application Settings'
        }
    ];

    // API Endpoints
    const apiEndpoints = [
        {
            method: 'GET',
            path: '/api/doctypes',
            description: 'Get all DocTypes',
            example: `curl -X GET /api/doctypes
            
Response:
{
  "data": [
    {
      "id": 1,
      "name": "user",
      "label": "User",
      "fields": [...]
    }
  ]
}`
        },
        {
            method: 'POST',
            path: '/api/doctypes',
            description: 'Create a new DocType',
            example: `curl -X POST /api/doctypes \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "product",
    "label": "Product",
    "fields": [
      {
        "fieldname": "name",
        "label": "Product Name",
        "fieldtype": "text",
        "required": true
      }
    ]
  }'`
        },
        {
            method: 'GET',
            path: '/api/doctypes/{doctype}/documents',
            description: 'Get documents for a DocType',
            example: `curl -X GET /api/doctypes/user/documents
            
Response:
{
  "data": [
    {
      "id": 1,
      "name": "john_doe",
      "display_name": "John Doe",
      "data": { "email": "john@example.com" }
    }
  ]
}`
        },
        {
            method: 'POST',
            path: '/api/doctypes/{doctype}/documents',
            description: 'Create a new document',
            example: `curl -X POST /api/doctypes/user/documents \\
  -H "Content-Type: application/json" \\
  -d '{
    "email": "jane@example.com",
    "name": "Jane Smith"
  }'`
        }
    ];

    // Methods
    const loadSampleDocType = (doctypeName) => {
        const doctype = sampleDocTypes.find(dt => dt.name === doctypeName);
        if (doctype) {
            selectedDocType.value = doctype;
            message.value = `Loaded ${doctype.label} form for preview`;
            messageType.value = 'default';

            // Clear message after 3 seconds
            setTimeout(() => {
                message.value = '';
            }, 3000);
        }
    };

    const handleFormSave = (data) => {
        console.log('Form saved:', data);
        message.value = 'Form data saved successfully!';
        messageType.value = 'default';

        // Clear message after 3 seconds
        setTimeout(() => {
            message.value = '';
        }, 3000);
    };

    const getMethodClass = (method) => {
        switch (method) {
            case 'GET': return 'bg-green-100 text-green-800';
            case 'POST': return 'bg-blue-100 text-blue-800';
            case 'PUT': return 'bg-yellow-100 text-yellow-800';
            case 'DELETE': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const getAlertClass = (type) => {
        return type === 'default'
            ? 'bg-green-50 border border-green-200'
            : 'bg-red-50 border border-red-200';
    };

    const getMessageTextClass = (type) => {
        return type === 'default' ? 'text-green-800' : 'text-red-800';
    };
</script>

<style scoped>
    /* Custom styles if needed */
</style>
