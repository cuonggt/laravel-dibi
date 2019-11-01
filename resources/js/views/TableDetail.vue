<template>
    <div>
        <a-menu
            v-model="current"
            mode="horizontal"
        >
            <a-menu-item key="tables.data">
                <router-link :to="{ name: 'tables.data', tableName: tableName }">
                    <a-icon type="ordered-list" />
                    <span>Data</span>
                </router-link>
            </a-menu-item>
            <a-menu-item key="tables.structure">
                <router-link :to="{ name: 'tables.structure', tableName: tableName }">
                    <a-icon type="layout" />
                    <span>Structure</span>
                </router-link>
            </a-menu-item>
            <a-menu-item key="tables.info">
                <router-link :to="{ name: 'tables.info', tableName: tableName }">
                    <a-icon type="info" />
                    <span>Table Info</span>
                </router-link>
            </a-menu-item>
        </a-menu>

        <div :style="{ paddingTop: '20px' }">
            <router-view />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            tableName: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                current: [this.$route.name || 'tables.data'],
            };
        },

        watch: {
            tableName: function () {
                this.current = [this.$route.name || 'tables.data'];
            },
        },
    };
</script>
