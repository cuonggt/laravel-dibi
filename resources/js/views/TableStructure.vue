<template>
    <loading-view :loading="loading">
        <a-table
            :data-source="fields"
            :columns="fieldColumns"
            :row-key="record => record.field"
            bordered
            :pagination=false
        >
            <span slot="nullable" slot-scope="nullable">{{ nullable ? 'YES' : 'NO' }}</span>
        </a-table>

        <div :style="{ paddingTop: '20px' }">
            <a-table
                :data-source="indexes"
                :columns="indexColumns"
                :row-key="record => record.keyName"
                bordered
                :pagination=false
            >
            </a-table>
        </div>
    </loading-view>
</template>

<script>
    export default {
        props: {
            tableName: {
                type: String,
                required: true,
            },
        },

        data() {
            return {
                fields: [],
                indexes: [],
                fieldColumns: [
                    {
                        title: 'Field',
                        dataIndex: 'field',
                    },
                    {
                        title: 'Type',
                        dataIndex: 'type',
                    },
                    {
                        title: 'Allow Null',
                        dataIndex: 'nullable',
                        scopedSlots: { customRender: 'nullable' },
                    },
                    {
                        title: 'Key',
                        dataIndex: 'key',
                    },
                    {
                        title: 'Default',
                        dataIndex: 'default',
                    },
                    {
                        title: 'Extra',
                        dataIndex: 'extra',
                    },
                ],
                indexColumns: [
                    {
                        title: 'Non_unique',
                        dataIndex: 'nonUnique',
                    },
                    {
                        title: 'Key_name',
                        dataIndex: 'keyName',
                    },
                    {
                        title: 'Seq_in_index',
                        dataIndex: 'seqInIndex',
                    },
                    {
                        title: 'Column_name',
                        dataIndex: 'columnName',
                    },
                    {
                        title: 'Collation',
                        dataIndex: 'collation',
                    },
                    {
                        title: 'Cardinality',
                        dataIndex: 'cardinality',
                    },
                    {
                        title: 'Sub_part',
                        dataIndex: 'subPart',
                    },
                    {
                        title: 'Packed',
                        dataIndex: 'packed',
                    },
                    {
                        title: 'Comment',
                        dataIndex: 'comment',
                    },
                ],
                loading: false,
            };
        },

        watch: {
            tableName: function () {
                this.fetchStructure();
            }
        },

        created() {
            this.fetchStructure();
        },

        methods: {
            async fetchStructure() {
                this.loading = true;

                const columnResponse = await axios.get('/dibi/api/tables/' + this.tableName + '/columns');

                const indexResponse = await axios.get('/dibi/api/tables/' + this.tableName + '/indexes');

                this.fields = columnResponse.data;
                this.indexes = indexResponse.data;
                this.loading = false;
            },
        },
    };
</script>
