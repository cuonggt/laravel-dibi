<template>
    <div class="grow flex flex-col overflow-x-auto">
        <div class="flex flex-col h-0 flex-1">
            <div class="bg-white w-full">
                <div class="px-12">
                    <div class="py-6">
                        <div class="flex items-center justify-between text-sm text-gray-700 uppercase font-bold tracking-widest">
                            Database {{ database }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex flex-col overflow-y-auto bg-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-gray-800">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            >
                                Name
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            >
                                Type
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            >
                                Engine
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            >
                                Encoding
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            >
                                Collation
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="table in tables"
                            :key="table.name"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <router-link :to="`/tables/${table.name}`">
                                    {{ table.name }}
                                </router-link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ table.type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ table.engine }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ table.encoding }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ table.collation }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            database: Dibi.database,
            tables: Dibi.tables,
        };
    },
};
</script>
