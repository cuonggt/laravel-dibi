<script type="text/ecmascript-6">
    import _ from 'lodash';

    export default {
        props: {
            table: String,
            required: true,
        },

        /**
         * The component's data.
         */
        data() {
            return {
                columns: [],
                rows: [],
                total: 0,
                count: 0,
                operators: [
                    '=', '<>', '>', '<', '>=', '<=', 'IN', 'LIKE', 'IS NULL', 'IS NOT NULL'
                ],
                searchForm: {
                    field: '',
                    operator: '=',
                    keyword: '',
                    sorting: '',
                    direction: 'asc',
                },
                selectedRow: null,
                selectedColumn: null,
                cellForm: {
                    value: '',
                    busy: false
                }
            };
        },

        /**
         * Prepare the component.
         */
        mounted() {
            this.refreshData();
        },

        computed: {
            isNullOrNotNullOperator: function () {
                return this.searchForm.operator == 'IS NULL' || this.searchForm.operator == 'IS NOT NULL';
            }
        },

        watch: {
            table: function () {
                this.refreshData();
                this.selectedRow = null;
                this.selectedColumn = null;
            }
        },

        methods: {
            /**
             * Reload the data.
             */
            refreshData() {
                this.loadColumns();
            },

            /**
             * Reset search form to default.
             */
            reset() {
                this.searchForm = {
                    field: this.columns[0].field,
                    operator: '=',
                    keyword: '',
                    sorting: this.columns[0].field,
                    direction: 'asc'
                };
                this.loadRows(false);
            },

            /**
             * Load the columns.
             */
            loadColumns() {
                return this.$http.get('/dibi/api/tables/' + this.table + '/columns')
                    .then(response => {
                        this.columns = response.data;
                        this.reset();
                    });
            },

            /**
             * Load the rows.
             */
            loadRows(filter = true) {
                let url = '/dibi/api/tables/' + this.table + '/rows?' +
                    'sorting=' + this.searchForm.sorting +
                    '&direction=' + this.searchForm.direction;

                if (filter) {
                    url += '&field=' + this.searchForm.field +
                        '&operator=' + this.searchForm.operator +
                        '&keyword=' + this.searchForm.keyword;
                }

                return this.$http.get(url)
                    .then(response => {
                        this.total = response.data.total;
                        this.count = response.data.count;
                        this.rows = response.data.data;
                    });
            },

            /**
             * If the operator is NULL or NOT NULL then reload the rows.
             */
            refreshDataIfNeeded() {
                if (this.isNullOrNotNullOperator) {
                    this.loadRows();

                    return true;
                }

                return false;
            },

            /**
             * Get header class with sorting direction.
             */
            sortingDirectionClassHeader(current, sorting, direction) {
                if (current != sorting) {
                    return 'sorting';
                }

                return 'sorting_' + direction;
            },

            /**
             * Reload the rows when update sorting.
             */
            updateSorting(sorting) {
                if (this.searchForm.sorting == sorting) {
                    if (this.searchForm.direction == 'asc') {
                        this.searchForm.direction = 'desc';
                    } else {
                        this.searchForm.direction = 'asc';
                    }
                } else {
                    this.searchForm.direction = 'asc';
                }

                this.searchForm.sorting = sorting;

                this.loadRows();
            },

            /**
             * Get row's __id__.
             */
            getRowId(row) {
                return _.get(row, '__id__');
            },

            /**
             * Show modal edit cell value.
             */
            onCellClick(row, column) {
                if (this.getRowId(this.selectedRow) !== this.getRowId(row)) {
                    this.selectedRow = row;
                } else {
                    this.selectedColumn = column;
                    this.cellForm = {
                        value: this.selectedRow[this.selectedColumn.field],
                        busy: false
                    };

                    $('#modal-edit-cell-value').modal('show');
                }
            },

            /**
             * Update cell value.
             */
            updateCell() {
                if (this.cellForm.value === this.selectedRow[this.selectedColumn.field]) {
                    $('#modal-edit-cell-value').modal('hide');

                    return false;
                }

                this.cellForm.busy = true;

                this.$http.put('/dibi/api/tables/' + this.table + '/rows', {
                    row: this.selectedRow,
                    column: this.selectedColumn,
                    value: this.cellForm.value
                }).then(response => {
                    this.selectedRow[this.selectedColumn.field] = this.cellForm.value;

                    this.rows = this.rows.map((row) => {
                        if (this.getRowId(row) === this.getRowId(this.selectedRow)) {
                            return this.selectedRow;
                        }

                        return row;
                    });

                    this.cellForm.busy = false;

                    $('#modal-edit-cell-value').modal('hide');
                });
            },

            /**
             * Update cell value to null.
             */
            updateCellWithNull() {
                this.cellForm.value = null;

                this.updateCell();
            }
        }
    };
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
                        <input type="search" class="form-control form-control-sm my-1 mr-2" placeholder="Search" v-model="searchForm.keyword" :disabled="isNullOrNotNullOperator">
                        <button type="submit" class="btn btn-primary btn-sm my-1 mr-2" @click.prevent="loadRows()">
                            <font-awesome-icon icon="search" /> Filter
                        </button>
                        <button type="submit" class="btn btn-outline-primary btn-sm my-1" @click.prevent="reset()">
                            <font-awesome-icon icon="bolt" /> Reset
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th
                                    v-for="(column, index) in columns"
                                    :key="index"
                                    :class="sortingDirectionClassHeader(column.field, searchForm.sorting, searchForm.direction)"
                                    @click.prevent="updateSorting(column.field)"
                                >
                                    {{ column.field }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in rows"
                                :key="index"
                                :class="{ 'row-selected': getRowId(selectedRow) === getRowId(row)}"
                            >
                                <td
                                    v-for="(column, index) in columns"
                                    :key="index"
                                    @click="onCellClick(row, column)"
                                >
                                    <span v-if="row[column.field] === null" class="text-null">NULL</span>
                                    <span v-else>{{ row[column.field] | str_limit }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="dataTables_info">
                            <span v-if="count < total">{{ count }} {{ count > 1 ? 'rows' : 'row' }} of {{ total }} match filter</span>
                            <span v-else>{{ total }} {{ total > 1 ? 'rows' : 'row' }} in table</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal-edit-cell-value" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" v-if="selectedRow && selectedColumn">
                        <div class="modal-header">
                            <h5 class="modal-title">Field: "{{ selectedColumn.field }}" - {{ selectedColumn.type }} {{ selectedColumn.nullable ? '' : 'NOT NULL' }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <textarea
                                    class="form-control"
                                    rows="7"
                                    v-model="cellForm.value"
                                ></textarea>
                            </div>

                            <div class="form-group">
                                <button
                                    type="button"
                                    class="btn btn-warning"
                                    :disabled="cellForm.busy"
                                    @click="updateCellWithNull()"
                                    v-if="selectedColumn.nullable"
                                >
                                    <font-awesome-icon icon="bullseye" /> Set NULL
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" :disabled="cellForm.busy" @click="updateCell()">
                                <font-awesome-icon icon="save" /> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
