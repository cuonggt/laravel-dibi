<script type="text/ecmascript-6">
    import MainHeader from './MainHeader.vue';
    import MainSidebar from './MainSidebar.vue';

    export default {
        components: {MainHeader, MainSidebar},
    }
</script>

<template>
    <div class="page-wrapper">
        <main-header />

        <div class="main-container">
            <main-sidebar />

            <div class="content">
                <slot />
            </div>
        </div>
    </div>
</template>
