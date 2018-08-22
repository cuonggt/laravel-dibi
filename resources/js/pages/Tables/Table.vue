<script type="text/ecmascript-6">
    import Layout from '../../layouts/MainLayout.vue';

    export default {
        props: ['table'],

        components: {Layout},
    }
</script>

<template>
    <layout>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <router-link :to="'/tables/' + this.table + '/rows'" active-class="active" class="nav-link">
                            <font-awesome-icon icon="list" /> Content
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="'/tables/' + this.table + '/columns'" active-class="active" class="nav-link">
                            <font-awesome-icon icon="columns" /> Structure
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :to="'/tables/' + this.table + '/info'" active-class="active" class="nav-link">
                            <font-awesome-icon icon="info" /> Table Info
                        </router-link>
                    </li>
                </ul>

                <router-view />
            </div>
        </div>
    </layout>
</template>
