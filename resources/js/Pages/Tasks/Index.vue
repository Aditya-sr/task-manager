<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';

defineProps({
    tasks: Object,
    filters: Object,
});

const page = usePage();
const flashMessage = ref(page.props.flash?.message || '');

watch(() => page.props.flash?.message, (newMessage) => {
    if (newMessage) {
        flashMessage.value = newMessage;
        setTimeout(() => {
            flashMessage.value = '';
        }, 3000);
    }
});

const search = ref(page.props.filters?.search || '');
const status = ref(page.props.filters?.status || 'All');

watch(search, debounce((value) => {
    router.get('/tasks', { search: value, status: status.value }, {
        preserveState: true,
        replace: true
    });
}, 300));

watch(status, (value) => {
    router.get('/tasks', { search: search.value, status: value }, {
        preserveState: true,
        replace: true
    });
});

const deleteTask = (id) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(`/tasks/${id}`, {
            onSuccess: () => {
                // Optional: show success message
            }
        });
    }
};

// ✅ Logout using Inertia router instead of form POST
const handleLogout = () => {
    router.post('/logout', {}, {
        onSuccess: () => {
            window.location.href = '/login';
        }
    });
};

const getFilePreviewType = (fileType) => {
    if (fileType.startsWith('image/')) return 'image';
    if (fileType === 'application/pdf') return 'pdf';
    return 'other';
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-800">Task Manager</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-700">{{ $page.props.auth.user?.name }}</span>
                        <!-- ✅ Changed to button with click handler -->
                        <button @click="handleLogout"
                                class="text-sm text-white bg-red-500 px-4 py-2 rounded hover:bg-red-600 transition">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Flash Message -->
            <div v-if="flashMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ flashMessage }}
            </div>

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search tasks..."
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <select v-model="status"
                                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="All">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <a href="/tasks/create"
                           class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                            + Create Task
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tasks List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Files</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ task.title }}</div>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ task.description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[
                                    'px-2 py-1 text-xs rounded-full',
                                    task.status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                ]">
                                    {{ task.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ task.due_date || 'No date' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <div v-for="file in task.files" :key="file.id">
                                        <img v-if="getFilePreviewType(file.file_type) === 'image'"
                                             :src="'/storage/' + file.file_path"
                                             class="h-10 w-10 object-cover rounded cursor-pointer border hover:opacity-75 transition"
                                             @click="window.open('/storage/' + file.file_path)">
                                        <span v-else-if="getFilePreviewType(file.file_type) === 'pdf'"
                                              class="cursor-pointer text-blue-500 text-xs hover:underline"
                                              @click="window.open('/storage/' + file.file_path)">
                                            📄 PDF
                                        </span>
                                    </div>
                                    <span v-if="task.files.length === 0" class="text-xs text-gray-400">No files</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium space-x-2">
                                <a :href="`/tasks/${task.id}/edit`"
                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <button @click="deleteTask(task.id)"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="tasks.data.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No tasks found. <a href="/tasks/create" class="text-blue-500 hover:underline">Create one!</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="tasks.links && tasks.data.length > 0" class="mt-4 flex justify-center">
                <div class="flex space-x-1">
                    <button
                        v-for="link in tasks.links"
                        :key="link.label"
                        v-html="link.label"
                        :disabled="!link.url"
                        @click="router.get(link.url, { search: search, status: status }, { preserveState: true })"
                        :class="[
                            'px-3 py-1 rounded text-sm',
                            link.active ? 'bg-blue-500 text-white' : 'bg-white border',
                            !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'
                        ]">
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
