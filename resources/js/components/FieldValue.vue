<template>
    <div>
        <span class="text-gray-400" v-if="value === null">NULL</span>
        <template v-else>
            <span class="text-gray-400" v-if="value === ''">EMPTY</span>
            <span>{{ value }}</span>
        </template>
    </div>
</template>

<script>
export default {
    props: ['value'],
};
</script>
