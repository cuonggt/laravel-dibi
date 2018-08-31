<script type="text/ecmascript-6">
    export default {
        props: ['table'],

        data() {
            return {
                columns: [],
                indexes: []
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
                this.loadIndexes();
            },

            loadColumns() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/columns')
                    .then(response => {
                        this.columns = response.data;
                    });
            },

            loadIndexes() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/indexes')
                    .then(response => {
                        this.indexes = response.data;
                    });
            }
        }
    }
</script>

<template>
    <div>
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
                                <td>{{ column.nullable ? 'YES' : 'NO' }}</td>
                                <td>{{ column.key }}</td>
                                <td>{{ column.default }}</td>
                                <td>{{ column.extra }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light">
                Indexes
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Non_unique</th>
                                <th>Key_name</th>
                                <th>Seq_in_index</th>
                                <th>Column_name</th>
                                <th>Collation</th>
                                <th>Cardinality</th>
                                <th>Sub_part</th>
                                <th>Packed</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(tableIndex, index) in indexes" :key="index">
                                <td>{{ tableIndex.nonUnique }}</td>
                                <td>{{ tableIndex.keyName }}</td>
                                <td>{{ tableIndex.seqInIndex }}</td>
                                <td>{{ tableIndex.columnName }}</td>
                                <td>{{ tableIndex.collation }}</td>
                                <td>{{ tableIndex.cardinality }}</td>
                                <td>{{ tableIndex.subPart === null ? 'NULL' : tableIndex.subPart }}</td>
                                <td>{{ tableIndex.packed === null ? 'NULL' : tableIndex.packed }}</td>
                                <td>{{ tableIndex.comment }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
