<template>
    <loading-view :loading="loadingView">
        <a-form layout="inline" v-if="! loadingView">
            <a-form-item label="Filter">
            </a-form-item>

            <a-form-item>
                <a-select :defaultValue="filterField"
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
                <a-select :defaultValue="filterOperator"
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
                <a-input :placeholder="filterValuePlaceholder"
                    v-model="filterValue"
                    :disabled="isNullOrNotNullOperator" />
            </a-form-item>

            <a-form-item>
                <a-button type="primary" @click.prevent="fetchData">Apply</a-button>
            </a-form-item>

            <a-form-item>
                <a-button type="default" @click.prevent="resetFilter">Reset</a-button>
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
                    '=', '<>', '>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE', 'NOT LIKE', 'IS NULL', 'IS NOT NULL',
                ],
                filterField: '',
                filterOperator: '=',
                filterValue: '',
                filterValuePlaceholder: 'EMPTY',
            };
        },

        created() {
            this.refreshData();
        },

        computed: {
            isNullOrNotNullOperator: function () {
                return this.filterOperator == 'IS NULL' || this.filterOperator == 'IS NOT NULL';
            }
        },

        watch: {
            tableName: function () {
                this.refreshData();
            },

            filterOperator: function (newVal) {
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

                this.resetFilter();
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
                    url += '&filter_field=' + this.filterField +
                        '&filter_operator=' + this.filterOperator +
                        '&filter_value=' + this.filterValue;
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
                this.filterField = value;
            },

            onChangeSearchOperator(value) {
                this.filterOperator = value;
            },

            /**
             * Reset filter form into default.
             */
            resetFilter() {
                this.filterField = this.columns[0].dataIndex;
                this.filterOperator = '=';
                this.filterValue = '';

                this.fetchData(false);
            },

            /**
             * Get row's __id__.
             */
            getRowId(row) {
                return _.get(row, '__id__');
            },
        },
    };
</script>
