<script type="text/ecmascript-6">
    export default {
        methods: {
            toggleSidebar() {
                $('body').toggleClass('sidebar-hidden');
            },

            toggleMobileSidebar() {
                $('body').toggleClass('sidebar-mobile-show');
            }
        }
    };
</script>

<template>
    <nav class="navbar page-header">
        <a class="btn btn-link sidebar-mobile-toggle d-md-none mr-auto" @click.prevent="toggleMobileSidebar()">
            <font-awesome-icon icon="bars" />
        </a>

        <router-link tag="a" to="/dashboard" class="navbar-brand">
            <font-awesome-icon icon="database" /> Dibi
        </router-link>

        <a class="btn btn-link sidebar-toggle d-md-down-none" @click.prevent="toggleSidebar()">
            <font-awesome-icon icon="bars" />
        </a>

        <ul class="navbar-nav ml-auto">
        </ul>
    </nav>
</template>
