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

// defineProps<{ canResetPassword?: boolean; status?: string; }>();

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value
}

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
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
                    <Label for="name">Name</Label>
                    <Input id="name" type="name" placeholder="Your name..." v-model="form.name" required autofocus
                        autocomplete="name" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
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
                <div class="relative mb-3">
                    <Label for="passwordConfirmation">Confirm Password</Label>
                    <div class="relative">
                        <Input id="passwordConfirmation" :type="showPasswordConfirmation ? 'text' : 'password'"
                            placeholder="Confirm Password" v-model="form.password_confirmation" required
                            autocomplete="new-password" />
                        <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2"
                            @click="togglePasswordConfirmationVisibility">
                            <EyeIcon v-if="!showPasswordConfirmation" class="h-4 w-4 text-gray-500" />
                            <EyeOffIcon v-else class="h-4 w-4 text-gray-500" />
                        </button>
                    </div>
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
                <Button type="submit" class="w-full" :disabled="form.processing">
                    <template v-if="form.processing">
                        <Loader2 class="w-4 h-4 mr-2 animate-spin" />
                        Please wait
                    </template>
                    <template v-else>
                        Register
                    </template>
                </Button>
                <Separator label="Atau" class="my-5 dark:bg-neutral-700" />
                <Button variant="outline" class="w-full mb-3">
                    Login dengan Google
                </Button>
                <div class="text-center text-sm">
                    Sudah memiliki akun?
                    <Button variant="link" as-child class="p-0 h-fit">
                        <Link :href="route('login')">
                        Login
                        </Link>
                    </Button>
                </div>
            </Form>
        </div>
    </div>
</template>


<!-- <script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template> -->
