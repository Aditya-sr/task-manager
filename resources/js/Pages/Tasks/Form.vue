<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    task: {
        type: Object,
        default: null,
    },
});

const isEditing = !!props.task;

const form = useForm({
    _method: props.task ? 'PUT' : 'POST',
    title: props.task?.title || '',
    description: props.task?.description || '',
    status: props.task?.status || 'Pending',
    due_date: props.task?.due_date || '',
    files: [],
    delete_files: [],
});

const filePreviews = ref([]);
const existingFiles = ref(props.task?.files || []);

const handleFileChange = (event) => {
    form.files = Array.from(event.target.files);
    filePreviews.value = [];

    for (let file of form.files) {
        const reader = new FileReader();
        reader.onload = (e) => {
            filePreviews.value.push({
                name: file.name,
                type: file.type,
                url: e.target.result,
            });
        };
        reader.readAsDataURL(file);
    }
};

const removeExistingFile = (fileId) => {
    form.delete_files.push(fileId);
    existingFiles.value = existingFiles.value.filter(f => f.id !== fileId);
};

const submit = () => {
    if (isEditing) {
        form.post(`/tasks/${props.task.id}`, {
            forceFormData: true,
            onSuccess: () => {
                filePreviews.value = [];
            },
        });
    } else {
        form.post('/tasks', {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                filePreviews.value = [];
            },
        });
    }
};

const getFilePreviewType = (fileType) => {
    if (fileType.startsWith('image/')) return 'image';
    if (fileType === 'application/pdf') return 'pdf';
    return 'other';
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-16">
                    <a href="/tasks" class="text-blue-500 hover:text-blue-700 mr-4">← Back</a>
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ isEditing ? 'Edit Task' : 'Create Task' }}
                    </h1>
                </div>
            </div>
        </nav>

        <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input v-model="form.title" type="text"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea v-model="form.description" rows="4"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                    </div>

                    <!-- Status & Due Date -->
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select v-model="form.status"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Pending">⏳ Pending</option>
                                <option value="Completed">✅ Completed</option>
                            </select>
                            <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                            <input v-model="form.due_date" type="date"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div v-if="form.errors.due_date" class="text-red-500 text-xs mt-1">{{ form.errors.due_date }}</div>
                        </div>
                    </div>

                    <div v-if="existingFiles.length > 0" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Files</label>
                        <div class="flex flex-wrap gap-3">
                            <div v-for="file in existingFiles" :key="file.id" class="relative group">
                                <img v-if="getFilePreviewType(file.file_type) === 'image'"
                                     :src="'/storage/' + file.file_path"
                                     class="h-24 w-24 object-cover rounded-lg border cursor-pointer"
                                     @click="window.open('/storage/' + file.file_path)">
                                <div v-else
                                     class="h-24 w-24 flex items-center justify-center bg-gray-100 rounded-lg border cursor-pointer"
                                     @click="window.open('/storage/' + file.file_path)">
                                    <span class="text-3xl">📄</span>
                                </div>
                                <button type="button" @click="removeExistingFile(file.id)"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition">
                                    ✕
                                </button>
                                <p class="text-xs text-gray-500 mt-1 truncate w-24">{{ file.file_name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ isEditing ? 'Add More Files' : 'Upload Files' }}
                            <span class="text-gray-400 text-xs">(Images: JPG, PNG, GIF | PDFs | Max 10MB each)</span>
                        </label>
                        <input type="file" multiple accept="image/*,.pdf"
                               @change="handleFileChange"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div v-if="form.errors.files" class="text-red-500 text-xs mt-1">{{ form.errors.files }}</div>
                        <div v-if="form.errors['files.*']" class="text-red-500 text-xs mt-1">{{ form.errors['files.*'] }}</div>

                        <!-- New File Previews -->
                        <div class="mt-3 flex flex-wrap gap-3">
                            <div v-for="(preview, index) in filePreviews" :key="index" class="relative">
                                <img v-if="preview.type.startsWith('image/')"
                                     :src="preview.url"
                                     class="h-24 w-24 object-cover rounded-lg border">
                                <div v-else class="h-24 w-24 flex items-center justify-center bg-gray-100 rounded-lg border">
                                    <span class="text-3xl">📄</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 truncate w-24">{{ preview.name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <a href="/tasks"
                           class="flex-1 text-center bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                        <button type="submit" :disabled="form.processing"
                                class="flex-1 bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 disabled:opacity-50 transition">
                            {{ isEditing ? '💾 Update Task' : '✨ Create Task' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
