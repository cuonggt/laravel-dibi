<template>
    <div>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Allow Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="column in columns">
                    <td>{{ column.field }}</td>
                    <td>{{ column.type }}</td>
                    <td>{{ column.nullable ? 'YES' : 'NO' }}</td>
                    <td>{{ column.key }}</td>
                    <td>{{ column.default }}</td>
                    <td>{{ column.extra }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table table-hover mt-4 mb-0">
            <thead>
                <tr>
                    <th>Non_unique</th>
                    <th>Key_name</th>
                    <th>Seq_in_index</th>
                    <th>Column_name</th>
                    <th>Collation</th>
                    <th>Cardinality</th>
                    <th>Sub_part</th>
                    <th>Packed</th>
                    <th>Comment</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="index in indexes">
                    <td>{{ index.nonUnique }}</td>
                    <td>{{ index.keyName }}</td>
                    <td>{{ index.seqInIndex }}</td>
                    <td>{{ index.columnName }}</td>
                    <td>{{ index.collation }}</td>
                    <td>{{ index.cardinality }}</td>
                    <td>{{ index.subPart }}</td>
                    <td>{{ index.packed }}</td>
                    <td>{{ index.comment }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['tableName', 'columns'],

    data() {
        return {
            indexes: [],
        };
    },

    created() {
        this.getTableStructure();
    },

    methods: {
        getTableStructure() {
            axios.get('/dibi/api/tables/' + this.tableName + '/indexes')
                .then(({ data }) => {
                    this.indexes = data;
                });
        },
    },
};
</script>
