<template>
    <loading-view :loading="loading">
        <div class="ant-table-wrapper">
            <div class="ant-spin-nested-loading">
                <div class="ant-spin-container">
                    <div class="ant-table ant-table-scroll-position-left ant-table-default ant-table-bordered">
                        <div class="ant-table-content">
                            <div class="ant-table-body">
                                <table>
                                    <tbody class="ant-table-tbody">
                                        <tr>
                                            <td scope="row">Type</td>
                                            <td>{{ info.engine }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Encoding</td>
                                            <td>{{ info.encoding }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Collation</td>
                                            <td>{{ info.collation }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Created at</td>
                                            <td>{{ info.createTime }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Updated at</td>
                                            <td>{{ info.updateTime }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Number of rows</td>
                                            <td>{{ formatNumber(info.rows) }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Row format</td>
                                            <td>{{ info.rowFormat }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Avg. row length</td>
                                            <td>{{ formatNumber(info.avgRowLength) }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Auto increment</td>
                                            <td>{{ formatNumber(info.autoIncrement) }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Data size</td>
                                            <td>{{ formatNumber(info.dataLength, '0.0 ib') }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Max data size</td>
                                            <td>{{ formatNumber(info.maxDataLength, '0 b') }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Index size</td>
                                            <td>{{ formatNumber(info.indexLength, '0 b') }}</td>
                                        </tr>
                                        <tr>
                                            <td scope="row">Free data size</td>
                                            <td>{{ formatNumber(info.dataFree, '0.0 ib') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                info: {},
                loading: false,
            };
        },

        created() {
            this.fetchInfo();
        },

        methods: {
            async fetchInfo() {
                this.loading = true;

                const response = await axios.get('/dibi/api/tables/' + this.tableName);

                this.info = response.data;
                this.loading = false;
            },
        },
    };
</script>
