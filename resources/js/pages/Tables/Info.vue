<script type="text/ecmascript-6">
    export default {
        props: ['table'],

        data() {
            return {
                data: {},
            };
        },

        mounted() {
            this.loadTable();
        },

        methods: {
            loadTable() {
                return this.$http.get('/dibi/api/tables/' + this.table)
                    .then(response => {
                        this.data = response.data;
                    });
            }
        }
    }
</script>

<template>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td scope="row">Type</td>
                            <td>{{ data.engine }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Encoding</td>
                            <td>{{ data.encoding }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Collation</td>
                            <td>{{ data.collation }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Created at</td>
                            <td>{{ data.createTime }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Updated at</td>
                            <td>{{ data.updateTime }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Number of rows</td>
                            <td>{{ formatNumber(data.rows) }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Row format</td>
                            <td>{{ data.rowFormat }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Avg. row length</td>
                            <td>{{ formatNumber(data.avgRowLength) }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Auto increment</td>
                            <td>{{ formatNumber(data.autoIncrement) }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Data size</td>
                            <td>{{ formatNumber(data.dataLength, '0.0 ib') }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Max data size</td>
                            <td>{{ formatNumber(data.maxDataLength, '0 b') }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Index size</td>
                            <td>{{ formatNumber(data.indexLength, '0 b') }}</td>
                        </tr>
                        <tr>
                            <td scope="row">Free data size</td>
                            <td>{{ formatNumber(data.dataFree, '0.0 ib') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
