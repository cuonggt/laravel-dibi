<script type="text/ecmascript-6">
    import Layout from '../layouts/MainLayout.vue';

    export default {
        components: {Layout},
    }
</script>

<template>
    <layout>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card p-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <span class="h4 d-block font-weight-normal mb-2">{count}</span>
                                <span class="font-weight-light">Total Tables</span>
                            </div>

                            <div class="h2 text-muted">
                                <font-awesome-icon icon="table" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>
