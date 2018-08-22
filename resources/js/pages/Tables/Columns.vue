<script type="text/ecmascript-6">
    export default {
        props: ['table'],

        data() {
            return {
                columns: [],
            };
        },

        mounted() {
            this.refreshData();
        },

        watch: {
            table: function () {
                this.refreshData();
            }
        },

        methods: {
            refreshData() {
                this.loadColumns();
            },

            loadColumns() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/columns')
                    .then(response => {
                        this.columns = response.data;
                    });
            }
        }
    }
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Allow Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(column, index) in columns" :key="index">
                            <td>{{ column.field }}</td>
                            <td>{{ column.type }}</td>
                            <td>{{ column.null }}</td>
                            <td>{{ column.key }}</td>
                            <td>{{ column.default }}</td>
                            <td>{{ column.extra }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
