<script setup lang="ts">
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    Inertia.post(route('users.store'), form.value);
};
</script>

<template>
    <div>
        <h1>Create User</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="name">Name</label>
                <input type="text" v-model="form.name" id="name">
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" v-model="form.email" id="email">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" v-model="form.password" id="password">
            </div>
            <div>
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" v-model="form.password_confirmation" id="password_confirmation">
            </div>
            <button type="submit">Create</button>
        </form>
    </div>
</template>
