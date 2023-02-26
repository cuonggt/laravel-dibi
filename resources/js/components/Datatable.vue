<template>
    <table class="divide-y divide-gray-200 text-gray-800">
        <thead class="bg-gray-50 sticky top-0">
            <tr>
                <th
                    v-for="column in columns"
                    :key="column.column_name"
                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider cursor-pointer"
                    @click.prevent="updateSorting(column.column_name)"
                >
                    <div class="flex justify-between items-center">
                        <span />
                        <span>{{ column.column_name }}</span>
                        <span>
                            <span v-if="column.column_name == sortKey">
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
                v-for="(record, index) in records"
                :key="index"
            >
                <td
                    v-for="column in columns"
                    :key="column.column_name"
                    class="px-6 py-4 whitespace-nowrap text-sm"
                >
                    <x-field-value :value="record[column.column_name] == null ? record[column.column_name] : strLimit(String(record[column.column_name]))" />
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        columns: Array,
        records: Array,
        sortKey: String,
        sortDir: {
            type: String,
            default: 'asc',
        },
        updateSorting: Function,
    },
};
</script>
