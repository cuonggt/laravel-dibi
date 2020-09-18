<template>
    <div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Table <strong>{{ tableName }}</strong></h5>
            </div>

            <ul class="nav nav-tabs mb-2">
                <li class="nav-item">
                    <router-link :to="{ name: 'tables.data', tableName: tableName }" class="nav-link" active-class="active">
                        Data
                    </router-link>
                </li>

                <li class="nav-item">
                    <router-link :to="{ name: 'tables.structure', tableName: tableName }" class="nav-link" active-class="active">
                        Structure
                    </router-link>
                </li>

                <li class="nav-item">
                    <router-link :to="{ name: 'tables.info', tableName: tableName }" class="nav-link" active-class="active">
                        Table Info
                    </router-link>
                </li>
            </ul>

            <router-view :columns="columns" />
        </div>
    </div>
</template>

<script>
    import fetchData from '@/fetchData';

    export default {
        beforeRouteEnter: fetchData(params => {
            return {
                inititalColumns: Dibi.path + '/api/tables/' + params.tableName + '/columns',
            };
        }),

        beforeRouteUpdate: fetchData(params => {
            return {
                columns: Dibi.path + '/api/tables/' + params.tableName + '/columns',
            };
        }),

        props: ['tableName', 'inititalColumns'],

        data() {
            return {
                columns: this.inititalColumns,
            };
        },
    };
</script>
