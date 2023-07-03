<template>
    <splitpanes
        horizontal
        class="default-theme"
    >
        <pane>
            <div class="flex flex-col h-full">
                <div class="grow">
                    <sql-editor v-model="query" />
                </div>
                <div class="sticky bottom-0 px-4 py-2">
                    <x-button
                        :disabled="!query || runningQuery"
                        @click.native="runQuery"
                    >
                        Run
                    </x-button>
                </div>
            </div>
        </pane>
        <pane style="overflow: scroll;">
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
        </pane>
    </splitpanes>
</template>

<script>
import { Splitpanes, Pane } from 'splitpanes';
import 'splitpanes/dist/splitpanes.css';

export default {
    components: { Splitpanes, Pane },

    data() {
        return {
            query: '',
            runningQuery: false,
            result: null,
        };
    },
    methods: {
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
