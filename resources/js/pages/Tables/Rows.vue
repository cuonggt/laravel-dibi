<script type="text/ecmascript-6">
    export default {
        props: ['table'],

        data() {
            return {
                columns: [],
                rows: [],
                operators: [
                    '=', '<>', '>', '<', '>=', '<=', 'IN', 'LIKE', 'IS NULL', 'IS NOT NULL'
                ],
                searchForm: {
                    field: '',
                    operator: '=',
                    keyword: '',
                }
            };
        },

        mounted() {
            this.refreshData();
        },

        computed: {
            keywordDisabled: function () {
                return this.searchForm.operator == 'IS NULL' || this.searchForm.operator == 'IS NOT NULL';
            }
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

            reset() {
                this.searchForm = {
                    field: this.columns[0].field,
                    operator: '=',
                    keyword: '',
                };
                this.loadRows(false);
            },

            loadColumns() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/columns')
                    .then(response => {
                        this.columns = response.data;
                        this.reset();
                    });
            },

            loadRows(filter = true) {
                let url = '/dibi/api/tables/' + this.table + '/rows';

                if (filter) {
                    url += '?field=' + this.searchForm.field +
                        '&operator=' + this.searchForm.operator +
                        '&keyword=' + this.searchForm.keyword;
                }

                return this.$http.get(url)
                    .then(response => {
                        this.rows = response.data;
                    });
            },

            refreshDataIfNeeded() {
                if (this.keywordDisabled) {
                    this.loadRows();

                    return true;
                }

                return false;
            }
        }
    }
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                <div class="dataTables_filter">
                    <form class="form-inline">
                        <label class="my-1 mr-2">Search: </label>
                        <select class="form-control form-control-sm my-1 mr-2" v-model="searchForm.field">
                            <option v-for="(column, index) in columns" :key="index" :value="column.field">{{ column.field }}</option>
                        </select>
                        <select class="form-control form-control-sm my-1 mr-2" v-model="searchForm.operator" @change="refreshDataIfNeeded()">
                            <option v-for="(operator, index) in operators" :key="index" :value="operator">{{ operator }}</option>
                        </select>
                        <input type="search" class="form-control form-control-sm my-1 mr-2" placeholder="Search" v-model="searchForm.keyword" :disabled="keywordDisabled">
                        <button type="submit" class="btn btn-primary btn-sm my-1 mr-2" @click.prevent="loadRows()">
                            <font-awesome-icon icon="search" /> Filter
                        </button>
                        <button type="submit" class="btn btn-outline-primary btn-sm my-1" @click.prevent="reset()">
                            <font-awesome-icon icon="bolt" /> Reset
                        </button>
                    </form>
                </div>
            </div>
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
