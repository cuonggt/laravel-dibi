<script type="text/ecmascript-6">
    export default {
        /**
         * The component's data.
         */
        data() {
            return {
                tables: [],
            };
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.loadTables();
        },

        methods: {
            /**
             * Load the tables.
             */
            loadTables() {
                return this.$http.get('/dibi/api/tables')
                    .then(response => {
                        this.tables = response.data;
                    });
            }
        }
    };
</script>

<template>
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-title">Tables</li>

                <li class="nav-item" v-for="(table, index) in tables" :key="index">
                    <router-link tag="a" :to="'/tables/' +  table.name" active-class="active" class="nav-link">
                        <font-awesome-icon icon="table" /> {{ table.name }}
                    </router-link>
                </li>
            </ul>
        </nav>
    </div>
</template>
