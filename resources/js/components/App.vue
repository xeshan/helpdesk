<template>
    <div :class="['app', theme]">
        <header class="app__header">
            <h1 class="app__title"><router-link to="/" class="app__title-link">Help Desk</router-link></h1>
            <nav class="app__nav">
                <router-link to="/dashboard" class="app__nav-link">Dashboard</router-link>
                <router-link to="/tickets" class="app__nav-link">Tickets</router-link>
            </nav>
            <button class="app__theme-toggle" @click="toggleTheme">{{ theme === 'dark' ? 'Light' : 'Dark' }} Mode</button>
        </header>
        <main class="app__main">
            <router-view />
        </main>
    </div>
</template>
<script>
    export default {
    name: 'App',
    data() {
        return { theme: localStorage.getItem('theme') || 'light' }
    },
    methods: {
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark'
            localStorage.setItem('theme', this.theme)
        },
    },
}
</script>
