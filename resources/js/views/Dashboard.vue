<template>
    <div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Database {{ database }}</h5>
            </div>

            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Engine</th>
                        <th>Encoding</th>
                        <th>Collation</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="table in tables">
                        <td>
                            <router-link :to="'/tables/' + table.name">{{ table.name }}</router-link>
                        </td>
                        <td>{{ table.type }}</td>
                        <td>{{ table.engine }}</td>
                        <td>{{ table.encoding }}</td>
                        <td>{{ table.collation }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            database: Dibi.database,
            tables: Dibi.tables,
        };
    },
};
</script>
