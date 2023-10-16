<template>
    <div>
        <table class="divide-y divide-gray-200 text-gray-800">
            <thead class="bg-gray-50 sticky top-0">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.columnName"
                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer"
                        @click.prevent="handleSorting(column.columnName)"
                    >
                        <div class="flex justify-between items-center">
                            <span />
                            <span>{{ column.columnName }}</span>
                            <span>
                                <span v-if="column.columnName == sortKey">
                                    <svg
                                        v-if="sortDir == 'asc'"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-3 w-3"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                        />
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200 overflow-y-auto">
                <tr
                    v-for="(row, index) in rows"
                    :key="index"
                    @click.prevent="selectRow(row)"
                >
                    <td
                        v-for="column in columns"
                        :key="column.columnName"
                        class="px-6 py-4 whitespace-nowrap text-sm"
                    >
                        <data-cell
                            :value="row[column.columnName]"
                            :column="column"
                        />
                    </td>
                </tr>
            </tbody>
        </table>

        <x-dialog-modal
            :show="selectedRow !== null"
            max-width="7xl"
        >
            <template #title>
                Row Details
            </template>
            <template #content>
                <vue-json-pretty :data="selectedRow" />
            </template>
            <template #footer>
                <x-secondary-button @click.native="selectedRow = null">
                    Close
                </x-secondary-button>
            </template>
        </x-dialog-modal>
    </div>
</template>

<script>
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

export default {
    components: {
        VueJsonPretty,
    },
    props: {
        columns: Array,
        rows: Array,
        sortKey: String,
        sortDir: {
            type: String,
            default: 'asc',
        },
        updateSorting: Function,
    },
    data() {
        return {
            selectedRow: null,
        };
    },
    methods: {
        selectRow(row) {
            this.selectedRow = row;
        },

        handleSorting(columnName) {
            if (this.updateSorting) {
                this.updateSorting(columnName);
            }
        },
    },
};
</script>
