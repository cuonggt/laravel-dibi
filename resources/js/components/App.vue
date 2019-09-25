<template>
    <a-layout id="layout-dibi">
        <a-layout-sider
            :trigger="null"
            collapsible
            v-model="collapsed"
        >
            <div class="logo">
                <a href="/dibi">
                    <a-icon type="database" />
                    <h1>Dibi</h1>
                </a>
            </div>

            <a-menu
                theme="dark"
                mode="inline"
                v-model="selectedMenu"
            >
                <a-menu-item v-for="table in tables" :key="table.name">
                    <router-link tag="a" :to="'/tables/' +  table.name">
                        <a-icon type="table" />
                        <span>{{ table.name }}</span>
                    </router-link>
                </a-menu-item>
            </a-menu>
        </a-layout-sider>

        <a-layout>
            <a-layout-header style="background: #fff; padding: 0">
                <a-icon
                    class="trigger"
                    :type="collapsed ? 'menu-unfold' : 'menu-fold'"
                    @click="() => collapsed = ! collapsed"
                />
            </a-layout-header>

            <a-layout-content :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '280px' }">
                <router-view />
            </a-layout-content>
        </a-layout>
    </a-layout>
</template>

<script>
    export default {
        data() {
            return {
                collapsed: false,
                tables: [],
                selectedMenu: this.$route.params.tableName ? [this.$route.params.tableName] : [],
            };
        },

        created() {
            this.fetchTables();
        },

        methods: {
            async fetchTables() {
                const response = await axios.get('/dibi/api/tables');

                this.tables = response.data;
            },
        },
    };
</script>

<style>
#layout-dibi {
    min-height: 100vh;
}

#layout-dibi .trigger {
    font-size: 18px;
    line-height: 64px;
    padding: 0 24px;
    cursor: pointer;
    transition: color .3s;
}

#layout-dibi .trigger:hover {
    color: #1890ff;
}

#layout-dibi .logo {
    position: relative;
    height: 64px;
    padding-left: 24px;
    overflow: hidden;
    line-height: 64px;
    background: #002140;
    -webkit-transition: all .3s;
    transition: all .3s;
}

#layout-dibi .logo h1, #layout-dibi .logo img, #layout-dibi .logo svg {
    display: inline-block;
    vertical-align: middle;
}

#layout-dibi .logo h1 {
    color: #fff;
    font-size: 20px;
    margin: 0 0 0 12px;
    font-weight: 600;
    vertical-align: middle;
}

#layout-dibi .logo svg {
    width: 32px;
    height: 32px;
}
</style>
