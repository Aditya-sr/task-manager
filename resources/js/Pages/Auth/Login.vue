<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

            <div v-if="$page.props.flash.message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ $page.props.flash.message }}
            </div>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-lg">
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded-lg">
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 disabled:opacity-50">
                    Login
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                Don't have an account?
                <a href="/register" class="text-blue-500 hover:underline">Register</a>
            </p>
        </div>
    </div>
</template>
