<template>
    <div class="flex-1 flex flex-col overflow-y-auto bg-gray-800">
        <div class="px-2 py-2">
            <x-input
                v-model="keyword"
                type="text"
                placeholder="Search for item..."
                class="w-full text-sm"
            />
        </div>

        <!-- Tables tabs -->
        <h3
            class="px-2 mt-4 text-xs leading-4 font-semibold text-gray-500 uppercase tracking-wider"
        >
            Tables
        </h3>

        <nav class="text-sm text-white">
            <router-link
                v-for="table in filteredTables"
                :key="`table-${table}`"
                :to="`/tables/${table}`"
                active-class="bg-gray-700 rounded-l-full border-r-4 border-blue-500 group mt-1"
                class="flex w-full pr-6 pl-4 py-2 items-center gap-x-4 hover:bg-gray-700 hover:rounded-l-full"
                :title="table"
            >
                <icon-table
                    size="6"
                    class="shrink-0"
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
