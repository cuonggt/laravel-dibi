<template>
    <div class="flex-1 flex flex-col overflow-y-auto bg-gray-800 soft-scroll">
        <!-- Tables tabs -->
        <h3
            class="px-3 mt-8 text-xs leading-4 font-semibold text-gray-500 uppercase tracking-wider"
        >
            Tables
        </h3>

        <div>
            <x-input
                id="filter-table"
                v-model="keyword"
                type="text"
                class="m-2 block"
                placeholder="Search..."
            />
        </div>

        <nav class="px-2 py-4 bg-gray-800">
            <router-link
                v-for="table in filteredTables"
                :key="`table-${table}`"
                :to="`/tables/${table}`"
                active-class="text-white bg-gray-900 focus:outline-none focus:bg-gray-900 transition ease-in-out duration-150"
                class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            >
                <icon-table
                    size="6"
                    class="mr-3 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
                />
                {{ table }}
            </router-link>
        </nav>
    </div>
</template>

<script>
export default {
    props: {
        tables: {
            type: Array,
            default: null,
        },
    },
    data() {
        return {
            keyword: '',
        };
    },
    computed: {
        filteredTables() {
            if (!this.keyword) {
                return this.tables;
            }
            return this.tables.filter((table) => table.includes(this.keyword));
        },
    },
};
</script>
