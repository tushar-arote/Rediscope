import KeysPreview from './screens/keys/preview';
import KeysIndex from './screens/keys/index';
import InfoIndex from './screens/info/index';

export default [
    {
        path: '/',
        redirect: '/keys'
    },

    {
        path: '/keys/:key',
        name: 'keys-preview',
        component: KeysPreview
    },

    {
        path: '/keys',
        name: 'Keys',
        component: KeysIndex
    },

    {
        path: '/information',
        name: 'Information',
        component: InfoIndex
    },
];
