<template>
    <splitpanes
        horizontal
        class="default-theme"
    >
        <pane style="overflow: scroll;">
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
        </pane>

        <pane style="overflow: scroll;">
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
        </pane>
    </splitpanes>
</template>

<script>
import { Splitpanes, Pane } from 'splitpanes';
import 'splitpanes/dist/splitpanes.css';

export default {
    components: { Splitpanes, Pane },

    props: {
        columns: Array,
        indexes: Array,
    },
};
</script>
