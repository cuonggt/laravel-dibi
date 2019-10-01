<template>
    <loading-view :loading="loadingView">
        <a-form layout="inline" v-if="! loadingView">
            <a-form-item label="Search:">
            </a-form-item>

            <a-form-item>
                <a-select :defaultValue="searchForm.field"
                    style="width: 120px"
                    @change="onChangeSearchField">
                    <a-select-option v-for="(column, key) in columns"
                        :key="key"
                        :value="column.dataIndex">
                        {{ column.dataIndex }}
                    </a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item>
                <a-select :defaultValue="searchForm.operator"
                    style="width: 120px"
                    @change="onChangeSearchOperator">
                    <a-select-option v-for="(operator, key) in operators"
                        :key="key"
                        :value="operator">
                        {{ operator }}
                    </a-select-option>
                </a-select>
            </a-form-item>

            <a-form-item>
                <a-input placeholder="Search"
                    v-model="searchForm.keyword"
                    :disabled="isNullOrNotNullOperator" />
            </a-form-item>

            <a-form-item>
                <a-button type="primary" @click.prevent="fetchData">Filter</a-button>
            </a-form-item>

            <a-form-item>
                <a-button type="default" @click.prevent="reset">Reset</a-button>
            </a-form-item>
        </a-form>

        <div :style="{ paddingTop: '20px' }">
            <a-table :columns="columns"
                :rowKey="record => getRowId(record)"
                :dataSource="data"
                :pagination="pagination"
                :loading="loadingData"
                :scroll="{ x: true }"
                @change="handleTableChange">
                <span slot="value" slot-scope="value">
                    <span v-if="value === null" class="text-null">NULL</span>
                    <span v-else>{{ value }}</span>
                </span>
            </a-table>
        </div>

        <div v-if="! loadingData">
            <p v-if="pagination.total < total">{{ pagination.total }} {{ pagination.total > 1 ? 'rows' : 'row' }} of {{ total }} match filter</p>
            <p v-else>{{ total }} {{ total > 1 ? 'rows' : 'row' }} in table</p>
        </div>

        <!-- <div class="modal fade" id="modal-edit-cell-value" tabindex="-1" role="dialog" aria-hidden="true">
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
        </div> -->
    </loading-view>
</template>

<script>
    export default {
        props: {
            tableName: String,
            required: true,
        },

        data() {
            return {
                data: [],
                pagination: {
                    current: 1,
                    pageSize: config.perPage,
                },
                filters: {},
                sorter: {},
                loadingView: false,
                loadingData: false,
                columns: [],
                total: 0,
                operators: [
                    '=', '<>', '>', '<', '>=', '<=', 'IN', 'LIKE', 'IS NULL', 'IS NOT NULL',
                ],
                searchForm: {
                    field: '',
                    operator: '=',
                    keyword: '',
                },
                selectedRow: null,
                selectedColumn: null,
                cellForm: {
                    value: '',
                    busy: false
                },
            };
        },

        created() {
            this.refreshData();
        },

        computed: {
            isNullOrNotNullOperator: function () {
                return this.searchForm.operator == 'IS NULL' || this.searchForm.operator == 'IS NOT NULL';
            }
        },

        watch: {
            tableName: function () {
                this.refreshData();
                this.selectedRow = null;
                this.selectedColumn = null;
            }
        },

        methods: {
            /**
             * Reload the data.
             */
            async refreshData() {
                this.loadingView = true;

                const columnResponse = await axios.get('/dibi/api/tables/' + this.tableName + '/columns');

                this.loadingView = false;

                this.columns = _.map(columnResponse.data, item => {
                    return {
                        title: item.field,
                        dataIndex: item.field,
                        sorter: true,
                        scopedSlots: { customRender: 'value' },
                    };
                });

                this.pagination.current = 1;

                this.sorter = {
                    column: this.columns[0],
                    columnKey: this.columns[0].dataIndex,
                    field: this.columns[0].dataIndex,
                    order: 'ascend',
                };

                this.searchForm = {
                    field: this.columns[0].dataIndex,
                    operator: '=',
                    keyword: '',
                };

                await this.fetchData();
            },

            handleTableChange(pagination, filters, sorter) {
                this.pagination = Object.assign(this.pagination, {...pagination});
                this.filters = Object.assign(this.filters, {...filters});
                this.sorter = Object.assign(this.sorter, {...sorter});

                this.fetchData();
            },

            async fetchData(filter = true) {
                this.loadingData = true;

                let url = '/dibi/api/tables/' + this.tableName + '/rows?' +
                    'page=' + this.pagination.current +
                    '&per_page=' + this.pagination.pageSize +
                    '&sort_key=' + this.sorter.field +
                    '&sort_direction=' + (this.sorter.order == 'ascend' ? 'asc' : 'desc');

                if (filter) {
                    url += '&field=' + this.searchForm.field +
                        '&operator=' + this.searchForm.operator +
                        '&keyword=' + this.searchForm.keyword;
                }

                const { data: { data, total, count } } = await axios.get(url);

                const pagination = { ...this.pagination };
                pagination.total = count;
                this.pagination = pagination;

                this.data = data;
                this.total = total;

                this.loadingData = false;
            },

            onChangeSearchField(value) {
                this.searchForm.field = value;
            },

            onChangeSearchOperator(value) {
                this.searchForm.operator = value;

                this.refreshDataIfNeeded();
            },

            /**
             * Reset search form to default.
             */
            reset() {
                this.searchForm = {
                    field: this.columns[0].dataIndex,
                    operator: '=',
                    keyword: '',
                };
                this.fetchData(false);
            },

            /**
             * If the operator is NULL or NOT NULL then reload the rows.
             */
            refreshDataIfNeeded() {
                if (this.isNullOrNotNullOperator) {
                    this.fetchData();

                    return true;
                }

                return false;
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
            // onCellClick(row, column) {
            //     if (this.getRowId(this.selectedRow) !== this.getRowId(row)) {
            //         this.selectedRow = row;
            //     } else {
            //         this.selectedColumn = column;
            //         this.cellForm = {
            //             value: this.selectedRow[this.selectedColumn.field],
            //             busy: false
            //         };

            //         $('#modal-edit-cell-value').modal('show');
            //     }
            // },

            /**
             * Update cell value.
             */
            // updateCell() {
            //     if (this.cellForm.value === this.selectedRow[this.selectedColumn.field]) {
            //         $('#modal-edit-cell-value').modal('hide');

            //         return false;
            //     }

            //     this.cellForm.busy = true;

            //     axios.put('/dibi/api/tables/' + this.tableName + '/rows', {
            //         row: this.selectedRow,
            //         column: this.selectedColumn,
            //         value: this.cellForm.value
            //     }).then(response => {
            //         this.selectedRow[this.selectedColumn.field] = this.cellForm.value;

            //         this.rows = this.rows.map((row) => {
            //             if (this.getRowId(row) === this.getRowId(this.selectedRow)) {
            //                 return this.selectedRow;
            //             }

            //             return row;
            //         });

            //         this.cellForm.busy = false;

            //         $('#modal-edit-cell-value').modal('hide');
            //     });
            // },

            /**
             * Update cell value to null.
             */
            // updateCellWithNull() {
            //     this.cellForm.value = null;

            //     this.updateCell();
            // }
        }
    };
</script>
