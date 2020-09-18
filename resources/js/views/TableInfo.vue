<template>
    <div>
        <table class="table table-hover mb-0">
            <tbody>
                <tr>
                    <th scope="row" style="width: 30%;">Type</th>
                    <td>{{ info.engine }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Encoding</th>
                    <td>{{ info.encoding }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Collation</th>
                    <td>{{ info.collation }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Created at</th>
                    <td>{{ info.createTime }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Updated at</th>
                    <td>{{ info.updateTime }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Number of rows</th>
                    <td>{{ formatNumber(info.rows) }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Row format</th>
                    <td>{{ info.rowFormat }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Avg. row length</th>
                    <td>{{ formatNumber(info.avgRowLength) }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Auto increment</th>
                    <td>{{ formatNumber(info.autoIncrement) }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Data size</th>
                    <td>{{ formatNumber(info.dataLength, '0.0 ib') }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Max data size</th>
                    <td>{{ formatNumber(info.maxDataLength, '0 b') }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Index size</th>
                    <td>{{ formatNumber(info.indexLength, '0 b') }}</td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%;">Free data size</th>
                    <td>{{ formatNumber(info.dataFree, '0.0 ib') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
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
                info: {},
                loading: false,
            };
        },

        mounted() {
            this.fetchInfo();
        },

        methods: {
            fetchInfo() {
                axios.get('/dibi/api/tables/' + this.tableName)
                    .then(({ data }) => {
                        this.info = data;
                    });
            },
        },
    };
</script>
