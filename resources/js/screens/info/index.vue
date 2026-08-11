<script type="text/ecmascript-6">
    import axios from 'axios';

    export default {
        data() {
            return {
                entry: null,
                batch: [],
                currentTab: 'Server'
            };
        }
    }
</script>

<template>
    <information-screen title="Information" resource="info" entry-point="true">
        <template slot="table-parameters" slot-scope="slotProps">
            <tr>
                <td class="table-fit font-weight-bold">Redis Version</td>
                <td>
                    {{slotProps.entry.Server.redis_version || '-'}}
                </td>
            </tr>

            <tr>
                <td class="table-fit font-weight-bold">Connected Clients</td>
                <td>
                    {{slotProps.entry.Clients.connected_clients || '-'}}
                </td>
            </tr>

            <tr>
                <td class="table-fit font-weight-bold">Total Output bytes</td>
                <td>
                    {{slotProps.entry.Stats.total_net_output_bytes || '-'}}
                </td>
            </tr>

            <tr>
                <td class="table-fit font-weight-bold">Cluster Enabled</td>
                <td>
                    {{slotProps.entry.Cluster.cluster_enabled || '-'}}
                </td>
            </tr>

            <tr>
                <td class="table-fit font-weight-bold">Used Memory</td>
                <td>
                    {{slotProps.entry.Memory.used_memory || '-'}}
                </td>
            </tr>
        </template>

        <div slot="after-attributes-card" slot-scope="slotProps">
            <div class="card mt-5">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Server'}" href="#" v-on:click.prevent="currentTab='Server'">Server</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Memory'}" href="#" v-on:click.prevent="currentTab='Memory'">Memory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Clients'}" href="#" v-on:click.prevent="currentTab='Clients'">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='CPU'}" href="#" v-on:click.prevent="currentTab='CPU'">CPU</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Stats'}" href="#" v-on:click.prevent="currentTab='Stats'">Stats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Keyspace'}" href="#" v-on:click.prevent="currentTab='Keyspace'">Keyspace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Replication'}" href="#" v-on:click.prevent="currentTab='Replication'">Replication</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Persistence'}" href="#" v-on:click.prevent="currentTab='Persistence'">Persistence</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="{active: currentTab=='Cluster'}" href="#" v-on:click.prevent="currentTab='Cluster'">Cluster</a>
                    </li>
                </ul>
                <div class="code-bg p-4 mb-0 text-white">
                    <vue-json-pretty :data="slotProps.entry.Server" :deep="3" v-if="currentTab=='Server'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Memory" :deep="3" v-if="currentTab=='Memory'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Clients" :deep="3" v-if="currentTab=='Clients'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.CPU" :deep="3" v-if="currentTab=='CPU'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Stats" :deep="3" v-if="currentTab=='Stats'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Keyspace" :deep="3" v-if="currentTab=='Keyspace'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Replication" :deep="3" v-if="currentTab=='Replication'"></vue-json-pretty>                    <vue-json-pretty :data="slotProps.entry.Replication" :deep="3" v-if="currentTab=='Replication'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Persistence" :deep="3" v-if="currentTab=='Persistence'"></vue-json-pretty>
                    <vue-json-pretty :data="slotProps.entry.Cluster" :deep="3" v-if="currentTab=='Cluster'"></vue-json-pretty>
                </div>
            </div>

        </div>
    </information-screen>
</template>
