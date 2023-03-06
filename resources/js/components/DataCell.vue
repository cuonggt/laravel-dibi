<template>
    <div :class="column.isStringDataType ? '' : 'text-right'">
        <span
            v-if="value === null"
            class="text-gray-400"
        >NULL</span>
        <template v-else>
            <span
                v-if="value === ''"
                class="text-gray-400"
            >EMPTY</span>
            <template v-else>
                <span
                    v-if="shouldHideValue"
                    class="cursor-pointer"
                    @click="toggleShowHiddenValue()"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"
                        />
                    </svg>
                </span>
                <span v-else>{{ displayedValue }}</span>
            </template>
        </template>
    </div>
</template>

<script>
export default {
    props: ['value', 'column'],

    data() {
        return {
            shouldHideValue: this.column.shouldHideValue,
        };
    },

    computed: {
        displayedValue() {
            return this.column.isStringDataType ? this.strLimit(this.value, 55) : this.value;
        },
    },

    methods: {
        toggleShowHiddenValue() {
            this.shouldHideValue = !this.shouldHideValue;
        },
    },
};
</script>
