<template>
    <div>
        <!-- <x-field-value :value="record[column.column_name] == null ? record[column.column_name] : strLimit(String(record[column.column_name]))" /> -->
        <span
            v-if="value === null"
            class="text-gray-400"
        >NULL</span>
        <template v-else>
            <span
                v-if="value === ''"
                class="text-gray-400"
            >EMPTY</span>
            <span>{{ value }}</span>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            default: null,
        },
        column: Object,
    },
};
</script>
