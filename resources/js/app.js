import Vue from 'vue';
import Base from './base';
import axios from 'axios';
import Routes from './routes';
import VueRouter from 'vue-router';
import VueJsonPretty from 'vue-json-pretty';
import moment from 'moment-timezone';
import Alert from './components/Alert.vue';
import IndexScreen from './components/IndexScreen.vue';
import PreviewScreen from './components/PreviewScreen.vue';
import InformationScreen from './components/InformationScreen.vue';

require('bootstrap');

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

Vue.use(VueRouter);

window.Popper = require('popper.js');

window.Bus = new Vue({ name: 'Bus' });

moment.tz.setDefault(Rediscope.timezone);

const router = new VueRouter({
    mode: 'history',
    routes: Routes,
    base: window.Rediscope.path,
});

Vue.component('alert', Alert);
Vue.component('index-screen', IndexScreen);
Vue.component('preview-screen', PreviewScreen);
Vue.component('information-screen', InformationScreen);
Vue.component('vue-json-pretty', VueJsonPretty);

Vue.mixin(Base);

new Vue({
    el: '#rediscope',

    router,

    data() {
        return {
            alert: {
                type: null,
                autoClose: 0,
                message: '',
                confirmationProceed: null,
                confirmationCancel: null,
            },
            connections: [],

            current: ""
        }
    },

    mounted() {
        console.log('in app mounted');
        this.setDefaultConnection();
        this.applyStoredTheme();

        axios.get('/' + Rediscope.path + '/api/connections'
        ).then(response => {
            this.connections = response.data;
        });
    },

    methods: {
        changeConnection() {
            //this.$redis.conn = this.current;

            localStorage.setItem("conn", this.current);

            Bus.$emit("connectionChanged");
        },

        setDefaultConnection() {
            this.current = localStorage.getItem("conn") || "default";

            //this.$redis.conn = this.current;
        },

        applyStoredTheme() {
            const theme = localStorage.getItem("rediscope-theme") || "app";
            const stylesheet = document.getElementById('rediscope-theme-stylesheet');

            if (stylesheet) {
                stylesheet.href = stylesheet.href.replace(/app(-dark)?\.css$/, theme + '.css');
            }
        },

        toggleTheme() {
            const stylesheet = document.getElementById('rediscope-theme-stylesheet');

            if (!stylesheet) {
                return;
            }

            const isDark = stylesheet.href.includes('app-dark.css');
            const theme = isDark ? 'app' : 'app-dark';

            stylesheet.href = stylesheet.href.replace(/app(-dark)?\.css$/, theme + '.css');
            localStorage.setItem("rediscope-theme", theme);
        }
    }
});
