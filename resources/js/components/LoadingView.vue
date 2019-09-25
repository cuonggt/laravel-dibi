<template>
    <div
        class="relative"
        :class="{'overflow-hidden' : loading}"
    >
        <div
            v-if="loading"
            :style="{
                paddingTop: 100,
                textAlign: 'center',
            }"
        >
            <a-spin size="large" />
        </div>

        <slot v-else />
    </div>
</template>

<script>
export default {
    props: {
        loading: {
            type: Boolean,
            default: true,
        },
    },
}
</script>
