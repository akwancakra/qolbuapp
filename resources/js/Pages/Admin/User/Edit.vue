<script setup lang="ts">
import { Inertia } from '@inertiajs/inertia';
</script>

<template>
    <div>
        <h1>Edit User</h1>
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
            <button type="submit">Update</button>
        </form>
    </div>
</template>

<script lang="ts">
export default {
    props: {
        user: Object,
    },
    data() {
        return {
            form: {
                name: this.user.name,
                email: this.user.email,
                password: '',
                password_confirmation: '',
            },
        };
    },
    methods: {
        submit() {
            Inertia.put(route('users.update', this.user.id), this.form);
        },
    },
};
</script>
