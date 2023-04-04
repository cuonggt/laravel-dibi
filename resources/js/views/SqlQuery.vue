<template>
    <div class="flex flex-col w-full">
        <div class="flex flex-col h-1/2">
            <div class="flex grow">
                <MonacoEditor @change="onChange" />
            </div>
            <div class="flex px-4 py-2">
                <x-button
                    :disabled="!query || runningQuery"
                    @click.native="runQuery"
                >
                    Run
                </x-button>
            </div>
        </div>
        <div class="flex h-1/2 bg-white">
            <div
                v-if="runningQuery"
                class="flex grow items-center justify-center"
            >
                <x-loader />
            </div>
            <div
                v-else
                class="px-4"
            >
                <div v-if="result">
                    {{ result }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MonacoEditor from 'monaco-editor-vue';

export default {
    components: {
        MonacoEditor,
    },
    data() {
        return {
            query: null,
            runningQuery: false,
            result: null,
        };
    },
    methods: {
        onChange(value) {
            this.query = value;
        },
        async runQuery() {
            this.runningQuery = true;
            try {
                const response = await axios.post(`${Dibi.path}/api/sql-query`, { sql_query: this.query });
                this.result = response.data.results.pop();
            } catch (e) {
                //
            }
            this.runningQuery = false;
        },
    },
};
</script>
