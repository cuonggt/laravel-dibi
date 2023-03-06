<template>
    <div class="flex flex-col">
        <div class="min-w-full overflow-x-auto">
            <table class="divide-y divide-gray-200 text-gray-800">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            #
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Column Name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Data Type
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Character Set
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Collation
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Is Nullable
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Column Default
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="(column, index) in columns"
                        :key="index"
                    >
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.ordinalPosition"
                                :column="{isStringDataType: false, shouldHideValue: false}"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.columnName"
                                :column="{isStringDataType: true, shouldHideValue: false}"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.dataType"
                                :column="{isStringDataType: true, shouldHideValue: false}"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.characterSetName"
                                :column="{isStringDataType: true, shouldHideValue: false}"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.collationName"
                                :column="{isStringDataType: false, shouldHideValue: false}"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            {{ column.isNullable ? 'YES' : 'NO' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <data-cell
                                :value="column.columnDefault"
                                :column="{isStringDataType: true, shouldHideValue: false}"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="min-w-full overflow-x-auto mt-4">
            <table class="divide-y divide-gray-200 text-gray-800">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Index Name
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Index Algorithm
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Is Unique
                        </th>
                        <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                        >
                            Column Name
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="index in indexes"
                        :key="index.index_name"
                    >
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ index.indexName }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ index.indexType }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ !index.nonUnique }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ index.columnName }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        columns: Array,
        indexes: Array,
    },
};
</script>
