<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Form } from '@/components/ui/form'
import { Loader2 } from 'lucide-vue-next'
import { Label } from '@/components/ui/label'
import { Head, Link, useForm } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'

defineProps<{ status?: string }>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <Head title="Forgot Password" />

    <div class="min-h-screen flex items-center justify-center sm:bg-neutral-100 dark:sm:bg-neutral-800">
        <div class="bg-white p-3 rounded-lg w-full dark:bg-neutral-700 sm:shadow-md sm:w-96">
            <h1 class="text-2xl font-semibold tracking-tight text-center mt-6 mb-3">Qolbu App</h1>
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Forgot your password? No problem. Just let us know your email
                address and we will email you a password reset link that will allow
                you to choose a new one.
            </div>
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>
            <Form @submit="submit">
                <div class="mb-3">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" placeholder="example@mail.com" v-model="form.email" required
                        autofocus autocomplete="email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <Button type="submit" class="w-full" :disabled="form.processing">
                    <template v-if="form.processing">
                        <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                        Please wait
                    </template>
                    <template v-else>
                        Email Password Reset Link
                    </template>
                </Button>
                <Button variant="link" as-child class="w-full text-center p-0 !leading-normal mt-3">
                    <Link :href="route('login')">
                    Kembali ke Login
                    </Link>
                </Button>
            </Form>
        </div>
    </div>
</template>
