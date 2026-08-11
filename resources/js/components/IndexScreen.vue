<script type="text/ecmascript-6">
    import $ from 'jquery';
    import _ from 'lodash';
    import axios from 'axios';
    import StylesMixin from './../mixins/entriesStyles';

    export default {
        mixins: [
            StylesMixin,
        ],

        props: [
            'resource', 'title'
        ],

        /**
         * The component's data.
         */
        data() {
            return {
                entries: [],
                ready: false,
                recordingStatus: 'enabled',
                key: '',
                isCheckAll: false,
                isHidden: false,
                keysdata: [],
                keys: [],
                hiddenkeys: '',
                deleteParams: '',
            };
        },

        /**
         * Prepare the component.
         */
        mounted() {
            console.log('in parent mounted');
            document.title = this.title + " - Rediscope";

            this.initiateRedis();
        },


        /**
         * Clean after the component is destroyed.
         */
        destroyed() {
            Bus.$off("connectionChanged");
        },

        watch: {
            '$route.query': function () {
                if (!this.$route.query.key) {
                    this.key = '';
                }

                this.ready = false;

                this.loadEntries((entries) => {
                    this.entries = entries;

                    this.ready = true;
                });
            },
        },

        methods: {
            initiateRedis(){
                this.ready = false;
                this.loadEntries((entries) => {
                    this.entries = entries;

                    this.ready = true;

                    this.recordingStatus = 'enabled';
                });

                Bus.$on("connectionChanged", data => {
                    this.loadEntries((entries) => {
                        this.entries = entries;
                    });
                });
            },

            loadEntries(after) {
                console.log('in LoadEntries');
                axios.get('/' + Rediscope.path + '/api/' + this.resource +
                    '?conn=' + localStorage.getItem("conn")+'&pattern='+this.key || "default"
                ).then(response => {

                    this.recordingStatus = response.data.status;

                    if (_.isFunction(after)) {
                        after(
                            response.data.entries
                        );
                    }

                    this.updateCheckall();
                })
            },

            /**
             * Search the entries of this type.
             */
            search(){
                this.debouncer(() => {
                    this.$router.push({query: _.assign({}, this.$route.query, {key: this.key})});
                });
            },

            checkAll: function(){
                console.log(this.hiddenkeys)
                this.isCheckAll = !this.isCheckAll;
                this.keys = [];
                if(this.isCheckAll){ // Check all
                    for (var key in this.entries) {
                        this.keys.push(this.entries[key].key);
                    }
                }
            },

            updateCheckall: function(){
                if(this.keys.length == this.entries.length){
                    this.isCheckAll = true;
                }else{
                    this.isCheckAll = false;
                }
            },

            del: function(){
                this.deleteParams = '';
                for (var key in this.keys) {
                    this.deleteParams+="&keys[]="+this.keys[key];
                }
                var _this = this;
                this.alertConfirm('Are you sure you want to delete selected keys?', ()=> {
                    axios.delete('/' + Rediscope.path + '/api/key?conn='+ localStorage.getItem("conn") + this.deleteParams).then(response => {
                        this.alertSuccess('Success : Keys Deleted!!');
                        _this.initiateRedis();
                        _this.keys = [];
                    }).catch(error => {
                        this.alertError('Error :'+ error);
                    });
                });
            }
        },
    }
</script>

<template>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="col-md-6">{{this.title}}</h5>

            <input type="text" class="form-control w-25"
                   v-if="key || entries.length > 0"
                   id="searchInput"
                   placeholder="Search Key" v-model="key" @input.stop="search">

            <div v-if="keys.length>0" @click="del">
                <svg class="fill-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                    <path d="M0 0h24v24H0z" fill="none"/>
                </svg>
            </div>
        </div>

        <div v-if="!ready" class="d-flex align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="icon spin mr-2 fill-text-color">
                <path d="M12 10a2 2 0 0 1-3.41 1.41A2 2 0 0 1 10 8V0a9.97 9.97 0 0 1 10 10h-8zm7.9 1.41A10 10 0 1 1 8.59.1v2.03a8 8 0 1 0 9.29 9.29h2.02zm-4.07 0a6 6 0 1 1-7.25-7.25v2.1a3.99 3.99 0 0 0-1.4 6.57 4 4 0 0 0 6.56-1.42h2.1z"></path>
            </svg>

            <span>Scanning...</span>
        </div>


        <div v-if="ready && entries.length == 0"
             class="d-flex flex-column align-items-center justify-content-center card-bg-secondary p-5 bottom-radius">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" class="fill-text-color" style="width: 200px;">
                <path fill-rule="evenodd"
                      d="M7 10h41a11 11 0 0 1 0 22h-8a3 3 0 0 0 0 6h6a6 6 0 1 1 0 12H10a4 4 0 1 1 0-8h2a2 2 0 1 0 0-4H7a5 5 0 0 1 0-10h3a3 3 0 0 0 0-6H7a6 6 0 1 1 0-12zm14 19a1 1 0 0 1-1-1 1 1 0 0 0-2 0 1 1 0 0 1-1 1 1 1 0 0 0 0 2 1 1 0 0 1 1 1 1 1 0 0 0 2 0 1 1 0 0 1 1-1 1 1 0 0 0 0-2zm-5.5-11a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm24 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm1 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm-14-3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm22-23a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM33 18a1 1 0 0 1-1-1v-1a1 1 0 0 0-2 0v1a1 1 0 0 1-1 1h-1a1 1 0 0 0 0 2h1a1 1 0 0 1 1 1v1a1 1 0 0 0 2 0v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 0-2h-1z"></path>
            </svg>

            <span>We didn't find anything - just empty space.</span>
        </div>


        <table id="indexScreen" class="table table-hover table-sm mb-0 penultimate-column-right"
               v-if="ready && entries.length > 0">
            <thead>
            <tr>
                <th><input type='checkbox' @click='checkAll()' v-model='isCheckAll'></th>
                <th scope="col">Type</th>
                <th scope="col">Key</th>
                <th scope="col">Expires</th>
                <th scope="col">Last Access</th>
                <th scope="col"></th>
            </tr>
            </thead>


            <transition-group tag="tbody" name="list">

                <tr v-for="entry in entries" :key="entry.key">
                    <td class="table-fit pr-0">
                    <input type='checkbox' v-bind:value='truncate(entry.key, 60)' v-model='keys' @change='updateCheckall()'>
                </td>
                <td class="table-fit pr-0">
                    <span class="badge font-weight-light" :class="'badge-'+keyTypeClass(entry.type)">
                        {{entry.type}}
                    </span>
                </td>

                <td :title="entry.key">{{truncate(entry.key, 60)}}</td>

                <td class="table-fit">
                    <span v-if="entry.ttl">{{entry.ttl}}</span>
                    <span v-else>-</span>
                </td>

                <td class="table-fit" :data-timeago="entry.idletime">{{(entry.idletime)}}</td>

                <td class="table-fit">
                    <router-link :to="{name:'keys-preview', params:{key: entry.key}}" class="control-action">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 16">
                            <path d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path>
                        </svg>
                    </router-link>
                </td>
                </tr>

            </transition-group>
        </table>

    </div>
</template>
