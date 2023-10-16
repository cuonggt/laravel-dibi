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
                <div
                    v-if="result"
                    class="flex min-w-full overflow-x-auto"
                >
                    <template
                        v-if="result.statement === 'select'"
                    >
                        <data-table
                            v-if="result.result.length > 0"
                            :columns="columns"
                            :rows="result.result"
                        />
                        <span v-else>No data</span>
                    </template>
                    <template v-else>
                        <span>Query OK<span v-if="result.statement !== 'statement'">: {{ result.result }} row affected</span></span>
                    </template>
                </div>
            </div>
        </pane>
    </splitpanes>
</template>

<script>
import { Splitpanes, Pane } from 'splitpanes';
import 'splitpanes/dist/splitpanes.css';
import 'vue-json-pretty/lib/styles.css';

export default {
    components: { Splitpanes, Pane },

    data() {
        return {
            query: '',
            runningQuery: false,
            result: null,
            columns: null,
        };
    },
    methods: {
        async runQuery() {
            this.runningQuery = true;
            try {
                const response = await axios.post(`${Dibi.path}/api/sql-query`, { sql_query: this.query });
                this.result = response.data.results.pop();
                if (this.result.statement == 'select' && Array.isArray(this.result.result) && this.result.result.length > 0) {
                    this.columns = _.map(Object.keys(this.result.result[0]), (key) => {
                        return {
                            columnName: key,
                        };
                    });
                }
            } catch (e) {
                //
            }
            this.runningQuery = false;
        },
    },
};
</script>
