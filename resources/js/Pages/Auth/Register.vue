<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-lg">
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-lg">
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input v-model="form.password" type="password" class="w-full px-3 py-2 border rounded-lg">
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input v-model="form.password_confirmation" type="password" class="w-full px-3 py-2 border rounded-lg">
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 disabled:opacity-50">
                    Register
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                Already have an account?
                <a href="/login" class="text-blue-500 hover:underline">Login</a>
            </p>
        </div>
    </div>
</template>
