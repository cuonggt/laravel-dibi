<template>
    <div class="grow flex flex-col overflow-x-auto">
        <div class="flex flex-col h-0 flex-1">
            <div class="bg-white w-full sticky top-0">
                <div class="px-12">
                    <div class="py-6">
                        <div class="flex items-center justify-between text-sm text-gray-700 uppercase font-bold tracking-widest">
                            <div>Table {{ currentTable.tableName }}</div>
                            <div>
                                <router-link to="/sql-query">
                                    <x-button>SQL</x-button>
                                </router-link>
                            </div>
                        </div>

                        <div
                            v-if="tab == 'data' && filterEnabled"
                            class="mt-6 border-t-2 border-gray-200"
                        >
                            <div class="flex flex-col w-full">
                                <form>
                                    <div class="flex mt-4">
                                        <select
                                            v-model="filterForm.field"
                                            class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-8"
                                        >
                                            <option
                                                v-for="column in currentTable.columns"
                                                :key="`column-name-${column.columnName}`"
                                                :value="column.columnName"
                                            >
                                                {{ column.columnName }}
                                            </option>
                                            <hr>
                                            <option value="__any__">
                                                Any column
                                            </option>
                                            <option value="__raw__">
                                                Raw SQL
                                            </option>
                                        </select>
                                        <select
                                            v-show="filterForm.field != '__raw__'"
                                            v-model="filterForm.operator"
                                            class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-8 ml-2"
                                        >
                                            <option value="=">
                                                =
                                            </option>
                                            <option value="<>">
                                                &lt;&gt;
                                            </option>
                                            <option value="<">
                                                &lt;
                                            </option>
                                            <option value=">">
                                                &gt;
                                            </option>
                                            <option value="<=">
                                                &lt;=
                                            </option>
                                            <option value=">=">
                                                &gt;=
                                            </option>
                                            <hr>
                                            <option value="IN">
                                                IN
                                            </option>
                                            <option value="NOT IN">
                                                NOT IN
                                            </option>
                                            <hr>
                                            <option value="IS NULL">
                                                IS NULL
                                            </option>
                                            <option value="IS NOT NULL">
                                                IS NOT NULL
                                            </option>
                                            <hr>
                                            <option value="BETWEEN">
                                                BETWEEN
                                            </option>
                                            <option value="NOT BETWEEN">
                                                NOT BETWEEN
                                            </option>
                                            <hr>
                                            <option value="LIKE">
                                                LIKE
                                            </option>
                                            <option value="NOT LIKE">
                                                NOT LIKE
                                            </option>
                                        </select>
                                        <x-input
                                            v-model="filterForm.value"
                                            class="flex-1 block w-full ml-2 px-4 py-2"
                                            :placeholder="filterValuePlaceholder"
                                            :disabled="isNullOrNotNullOperator"
                                        />
                                        <x-button
                                            class="ml-2"
                                            :disabled="loadingRecords"
                                            @click.native="loadRecords"
                                        >
                                            Apply
                                        </x-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="flex-1 flex flex-col bg-gray-200 overflow-y-auto"
            >
                <template v-if="tab == 'data'">
                    <div class="flex min-w-full overflow-x-auto">
                        <data-table
                            :columns="currentTable.columns"
                            :rows="records"
                            :sort-key="sortKey"
                            :sort-dir="sortDir"
                            :update-sorting="updateSorting"
                        />
                    </div>
                </template>

                <table-structure
                    v-if="tab == 'structure'"
                    :columns="currentTable.columns"
                    :indexes="currentTable.indexes"
                />
            </div>

            <div class="bg-gray-100 border-t-2 sticky bottom-0">
                <div class="px-12">
                    <div class="py-4">
                        <div class="flex justify-between items-center">
                            <div class="flex">
                                <button
                                    class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 rounded-r-none"
                                    :class="tab == 'data' ? 'bg-gray-800 border-transparent text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-75' : 'bg-white border-gray-300 text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50'"
                                    :disabled="tab == 'data'"
                                    @click="tab = 'data'"
                                >
                                    Data
                                </button>
                                <button
                                    class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 rounded-l-none rounded-r-none"
                                    :class="tab == 'structure' ? 'bg-gray-800 border-transparent text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-75' : 'bg-white border-gray-300 text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50'"
                                    :disabled="tab == 'structure'"
                                    @click="tab = 'structure'"
                                >
                                    Structure
                                </button>
                            </div>
                            <div
                                v-if="tab == 'data'"
                                class="text-sm text-gray-700"
                            >
                                <span v-if="loadingRecords">Loading rows...</span>
                                <span v-else>
                                    <span v-if="total > 0">
                                        {{ formatNumber(from) }}-{{ formatNumber(to) }} of {{ formatNumber(total) }} rows
                                    </span>
                                    <span v-else>
                                        0 rows
                                    </span>
                                </span>
                            </div>
                            <div
                                v-if="tab == 'data'"
                                class="flex gap-x-4"
                            >
                                <div>
                                    <button
                                        class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150"
                                        :class="filterEnabled ? 'bg-gray-800 border-transparent text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-75' : 'bg-white border-gray-300 text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50'"
                                        @click="toggleFilter"
                                    >
                                        Filters
                                    </button>
                                </div>
                                <div class="flex">
                                    <button
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out rounded-r-none disabled:opacity-25"
                                        title="Previous page"
                                        :disabled="offset == 0 || loadingRecords"
                                        @click.prevent="selectPreviousPage"
                                    >
                                        <icon-chevron-left size="4" />
                                    </button>
                                    <button
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out rounded-l-none rounded-r-none"
                                        title="Page settings"
                                        :disabled="loadingRecords"
                                        @click.prevent="startSetting"
                                    >
                                        <icon-cog size="4" />
                                    </button>
                                    <button
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out rounded-l-none disabled:opacity-25"
                                        title="Next page"
                                        :disabled="!hasMorePages || loadingRecords"
                                        @click.prevent="selectNextPage"
                                    >
                                        <icon-chevron-right size="4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-dialog-modal
            :show="setting"
            max-width="sm"
        >
            <template #title>
                Page settings
            </template>
            <template #content>
                <form @submit.prevent="setPageSettings">
                    <div>
                        <x-label
                            for="limit"
                            value="Limit"
                        />
                        <x-input
                            id="limit"
                            v-model="pageSettingsForm.limit"
                            type="text"
                            class="mt-1 block w-full"
                            placecholder="Limit"
                            required
                            autofocus
                        />
                    </div>
                    <div class="mt-4">
                        <x-label
                            for="offset"
                            value="Offset"
                        />
                        <x-input
                            id="offset"
                            v-model="pageSettingsForm.offset"
                            type="text"
                            class="mt-1 block w-full"
                            placecholder="Offset"
                            required
                        />
                    </div>
                </form>
            </template>
            <template #footer>
                <x-secondary-button @click.native="closePageSettingsModal">
                    Nevermind
                </x-secondary-button>

                <x-button
                    class="ml-2"
                    :class="{ 'opacity-25': loadingRecords }"
                    :disabled="loadingRecords"
                    @click.native="setPageSettings"
                >
                    Go
                </x-button>
            </template>
        </x-dialog-modal>
    </div>
</template>

<script>
export default {
    props: {
        tableName: String,
        currentTable: Object,
    },

    data() {
        return {
            tab: 'data',
            records: [],
            total: 0,
            offset: 0,
            limit: 50,
            sortKey: null,
            sortDir: 'asc',
            loadingRecords: true,
            setting: false,
            pageSettingsForm: {
                offset: null,
                limit: null,
            },
            filterEnabled: false,
            filterForm: {
                field: '__raw__',
                operator: '=',
                value: '',
            },
            filterValuePlaceholder: 'EMPTY',
        };
    },

    computed: {
        from() {
            return this.offset + 1;
        },

        to() {
            return this.offset + this.records.length;
        },

        hasMorePages() {
            return this.offset + this.records.length < this.total;
        },

        isNullOrNotNullOperator() {
            return ['IS NULL', 'IS NOT NULL'].includes(this.filterForm.operator);
        },
    },

    watch: {
        'filterForm.operator': function (newVal) {
            if (['IN', 'NOT IN'].includes(newVal)) {
                this.filterValuePlaceholder = '1,2,3';
            } else if (['IS NULL', 'IS NOT NULL'].includes(newVal)) {
                this.filterForm.value = '';
                this.filterValuePlaceholder = '';
            } else if (['BETWEEN', 'NOT BETWEEN'].includes(newVal)) {
                this.filterValuePlaceholder = '1 AND 100';
            } else if (['LIKE', 'NOT LIKE'].includes(newVal)) {
                this.filterValuePlaceholder = 'Pattern';
            } else {
                this.filterValuePlaceholder = 'EMPTY';
            }
        },
    },

    mounted() {
        this.loadRecords();
    },

    methods: {
        async loadRecords() {
            this.loadingRecords = true;

            const data = this.filterEnabled ? {
                filters: [
                    this.filterForm,
                ],
            } : {};

            const queryParams = this.httpBuildQuery({
                offset: this.offset,
                limit: this.limit,
                sort_key: this.sortKey ? this.sortKey : '',
                sort_dir: this.sortDir,
            });

            try {
                const response = await axios.post(
                    `${Dibi.path}/api/tables/${this.tableName}/rows/filter?${queryParams}`,
                    data,
                );

                this.records = response.data.data;
                this.total = response.data.total;
                this.loadingRecords = false;
            } catch (error) {
                this.loadingRecords = false;
            }
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

            this.loadRecords();
        },

        selectPreviousPage() {
            if (this.offset > this.limit) {
                this.offset = this.offset - this.limit;
            } else {
                this.offset = 0;
            }

            this.loadRecords();
        },

        selectNextPage() {
            this.offset = this.offset + this.limit;

            this.loadRecords();
        },

        startSetting() {
            this.pageSettingsForm = {
                limit: this.limit,
                offset: this.offset,
            };
            this.setting = true;
        },

        setPageSettings() {
            this.limit = parseInt(this.pageSettingsForm.limit);
            this.offset = parseInt(this.pageSettingsForm.offset);
            this.loadRecords();
            this.closePageSettingsModal();
        },

        closePageSettingsModal() {
            this.setting = false;
        },

        toggleFilter() {
            this.filterEnabled = !this.filterEnabled;

            if (! this.filterEnabled) {
                this.loadRecords();
            }
        },
    },
};
</script>
