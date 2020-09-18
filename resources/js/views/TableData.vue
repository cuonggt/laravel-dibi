<template>
    <div>
        <div class="card-body">
            <form class="form-inline">
                <label class="my-1 mr-2">Filter</label>

                <select class="custom-select my-1 mr-2"
                    v-model="filterForm.field">
                    <option v-for="column in columns"
                        :key="column.field"
                        :value="column.field">{{ column.field }}</option>
                </select>

                <select class="custom-select my-1 mr-2"
                    v-model="filterForm.operator">
                    <option v-for="(operator, key) in operators"
                        :key="key"
                        :value="operator">{{ operator }}</option>
                </select>

                <input type="text"
                    class="form-control my-1 mr-2"
                    v-model="filterForm.value"
                    :placeholder="filterValuePlaceholder"
                    :disabled="isNullOrNotNullOperator" />

                <button type="submit"
                    class="btn btn-primary my-1 mr-2"
                    @click.prevent="applyFilter"
                    :disabled="filterForm.processing">Apply</button>

                <button
                    class="btn btn-secondary my-1 mr-2"
                    @click.prevent="resetFilterForm(false)"
                    :disabled="filterForm.processing">Reset</button>
            </form>
        </div>

        <div class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius" v-if="! ready">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
            </svg> <span>Loading...</span>
        </div>

        <div class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius" v-if="ready && total == 0">
            <span>There aren't any rows.</span>
        </div>

        <div class="dataTables_wrapper dt-bootstrap4" v-if="ready && total > 0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 dataTable">
                    <thead>
                        <tr>
                            <th v-for="column in columns"
                                :key="column.field"
                                :class="sortingDirectionClassHeader(column.field, sortKey, sortDir)"
                                @click.prevent="updateSorting(column.field)">{{ column.field }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item in items">
                            <td v-for="column in columns">
                                <span v-if="item[column.field] === null" class="text-null">NULL</span>
                                <span v-else>{{ item[column.field] }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 mb-4">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info">
                        Showing {{ from }} to {{ to }} of {{ total }} rows
                    </div>
                </div>

                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate">

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['tableName', 'columns'],

        data() {
            return {
                ready: false,
                items: [],
                from: 0,
                to: 0,
                total: 0,
                operators: [
                    '=', '<>', '>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'IS NULL', 'IS NOT NULL',
                ],
                filtering: false,
                filterForm: {
                    field: this.columns[0].field,
                    operator: '=',
                    value: '',
                    processing: false,
                },
                page: 1,
                perPage: 15,
                sortKey: this.columns[0].field,
                sortDir: 'asc',
                filterValuePlaceholder: 'EMPTY',
            };
        },

        created() {
            this.getRows(true);
        },

        computed: {
            isNullOrNotNullOperator: function () {
                return this.filterForm.operator == 'IS NULL' || this.filterForm.operator == 'IS NOT NULL';
            }
        },

        watch: {
            tableName: function () {
                this.resetFilterForm(true);
            },

            'filterForm.operator': function (newVal) {
                if (['IN', 'NOT IN'].includes(newVal)) {
                    this.filterValuePlaceholder = '1,2,3';
                } else if (['IS NULL', 'IS NOT NULL'].includes(newVal)) {
                    this.filterValue = '';
                    this.filterValuePlaceholder = '';
                } else if (['LIKE', 'NOT LIKE'].includes(newVal)) {
                    this.filterValuePlaceholder = 'Pattern';
                } else {
                    this.filterValuePlaceholder = 'EMPTY';
                }
            },
        },

        methods: {
            resetFilterForm(refreshing = false) {
                this.filterForm = {
                    field: this.columns[0].field,
                    operator: '=',
                    value: '',
                    processing: false,
                };

                this.page = 1;
                this.perPage = 15;
                this.sortKey = this.columns[0].field;
                this.sortDir = 'asc';

                this.filtering = false;

                this.getRows(refreshing);
            },

            getRows(refreshing = false) {
                if (refreshing) {
                    this.ready = false;
                }

                let url = '/dibi/api/tables/' + this.tableName + '/rows?' +
                    'page=' + this.page +
                    '&per_page=' + this.perPage +
                    '&sort_key=' + this.sortKey +
                    '&sort_direction=' + this.sortDir;

                if (this.filtering) {
                    url += '&filter_field=' + this.filterForm.field +
                        '&filter_operator=' + this.filterForm.operator +
                        '&filter_value=' + this.filterForm.value;
                }

                this.filterForm.processing = true;

                axios.get(url)
                    .then(response => {
                        const { data, from, to, total } = response.data;

                        this.items = data;
                        this.from = from;
                        this.to = to;
                        this.total = total;

                        this.ready = true;
                        this.filterForm.processing = false;
                    });
            },

            applyFilter() {
                this.filtering = true;
                this.getRows(false);
            },

            /**
             * Get row's __id__.
             */
            getRowId(row) {
                return _.get(row, '__id__');
            },

            updateSorting(sortKey) {
                if (this.sortKey == sortKey) {
                    if (this.sortDir == 'asc') {
                        this.sortDir = 'desc';
                    } else {
                        this.sortDir = 'asc';
                    }
                } else {
                    this.sortDir = 'asc';
                }

                this.sortKey = sortKey;

                this.getRows(false);
            },

            sortingDirectionClassHeader(current, sorting, direction) {
                if (current != sorting) {
                    return 'sorting';
                }

                return 'sorting_' + direction;
            },
        },
    };
</script>
