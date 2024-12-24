<template>
    <div class="grow flex flex-col overflow-x-auto">
        <div class="flex flex-col h-0 flex-1">
            <div class="bg-white w-full">
                <div class="px-12">
                    <div class="py-6">
                        <div class="flex items-center justify-between text-sm text-gray-700 uppercase font-bold tracking-widest">
                            <div>
                                <div v-if="connections.length > 1">
                                    <select
                                        class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-8"
                                        @change="selectConnection($event.target.value)"
                                    >
                                        <option
                                            v-for="connection in connections"
                                            :key="connection"
                                            :value="connection"
                                            :selected="connection == currentDatabaseConnection"
                                        >
                                            {{ connection }}
                                        </option>
                                    </select>
                                </div>
                                <div v-else>
                                    {{ connections[0] }}
                                </div>
                            </div>
                            <div>Database {{ database }}</div>
                            <div>
                                <router-link to="/sql-query">
                                    <x-button>SQL</x-button>
                                </router-link>
                            </div>
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
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="table in tables"
                            :key="table.tableName"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <router-link :to="`/tables/${table.tableName}`">
                                    {{ table.tableName }}
                                </router-link>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ table.tableType }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            connections: Dibi.databaseConnections,
            currentDatabaseConnection: Dibi.currentDatabaseConnection,
            database: Dibi.database,
            tables: Dibi.informationSchema.tables,
        };
    },
    methods: {
        async selectConnection(connection) {
            if (connection == this.currentDatabaseConnection) {
                return;
            }

            await axios.post(`${Dibi.path}/api/select-connection`, {
                connection,
            });

            window.location.href = Dibi.path;
        },
    },
};
</script>
