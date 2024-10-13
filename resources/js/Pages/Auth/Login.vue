<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Checkbox } from '@/Components/ui/checkbox'
import { Separator } from '@/Components/ui/separator'
import { Form } from '@/Components/ui/form'
import { EyeIcon, EyeOffIcon, Loader2 } from 'lucide-vue-next'
import { Label } from '@/components/ui/label'
import { Head, Link, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'

defineProps<{ canResetPassword?: boolean; status?: string; }>();

const showPassword = ref(false)

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>

    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center sm:bg-neutral-100 dark:sm:bg-neutral-800">
        <div class="bg-white p-3 rounded-lg w-full dark:bg-neutral-700 sm:shadow-md sm:w-96">
            <h1 class="text-2xl font-semibold tracking-tight text-center my-6">Qolbu App</h1>
            <Form @submit="submit">
                <div class="mb-3">
                    <Label for="email">Email</Label>
                    <Input id="email" type="email" placeholder="example@mail.com" v-model="form.email" required
                        autofocus autocomplete="email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
                <div class="relative mb-3">
                    <Label for="password">Password</Label>
                    <div class="relative">
                        <Input id="password" :type="showPassword ? 'text' : 'password'" placeholder="Password"
                            v-model="form.password" required autocomplete="password" />
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2"
                            @click="togglePasswordVisibility">
                            <EyeIcon v-if="!showPassword" class="h-4 w-4 text-gray-500" />
                            <EyeOffIcon v-else class="h-4 w-4 text-gray-500" />
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
                <div class="flex items-center mb-3">
                    <Checkbox v-model:checked="form.remember" id="remember" />
                    <label for="remember" class="ml-2 text-sm">
                        Ingat saya
                    </label>
                </div>
                <Button type="submit" class="w-full" :disabled="form.processing">
                    <template v-if="form.processing">
                        <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                        Please wait
                    </template>
                    <template v-else>
                        Login
                    </template>
                </Button>
                <Separator label="Atau" class="my-5 dark:bg-neutral-700" />
                <Button variant="outline" class="w-full mb-3">
                    Login dengan Google
                </Button>
                <div class="text-center text-sm mb-2">
                    <Button variant="link" as-child class="p-0 h-fit">
                        <Link v-if="canResetPassword" :href="route('password.request')">
                        Forgot password?
                        </Link>
                    </Button>
                </div>
                <div class="text-center text-sm">
                    Don't have an account?
                    <Button variant="link" as-child class="p-0 h-fit">
                        <Link :href="route('register')">
                        Register
                        </Link>
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>
