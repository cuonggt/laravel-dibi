<script type="text/ecmascript-6">
    export default {
        props: ['table'],

        data() {
            return {
                columns: [],
                rows: [],
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
                this.loadRows();
            },

            loadColumns() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/columns')
                    .then(response => {
                        this.columns = response.data;
                    });
            },

            loadRows() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/rows')
                    .then(response => {
                        this.rows = response.data;
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
                            <th v-for="(column, index) in columns" :key="index">
                                {{ column.field }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in rows" :key="index">
                            <td v-for="(column, index) in columns" :key="index">
                                {{ row[column.field] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
