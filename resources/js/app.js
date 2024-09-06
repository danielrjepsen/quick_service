require('./bootstrap');

// Import modules...
import Vue from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue';
import PortalVue from 'portal-vue';
import '../css/element-ui-theme/index.css' // Custom theme - made with https://element.eleme.io/#/en-US/theme
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import VueCurrencyFilter from 'vue-currency-filter'
import lang from 'element-ui/lib/locale/lang/da'
import locale from 'element-ui/lib/locale'
import VueI18n from 'vue-i18n';
import da from '../lang/da/da.json'

// configure language
locale.use(lang)
import VueScrollactive from 'vue-scrollactive';

import { DateTime } from "luxon";
window.$DateTime = DateTime

Vue.mixin({ methods: { route } });
Vue.use(InertiaPlugin);
Vue.use(PortalVue);
Vue.use(ElementUI);
Vue.use(VueCurrencyFilter, {
    name: 'currency_dkk',
    symbol: 'kr',
    thousandsSeparator: '.',
    fractionCount: 0,
    fractionSeparator: ',',
    symbolPosition: 'back',
    symbolSpacing: true,
    avoidEmptyDecimals: undefined,
})
Vue.use(VueScrollactive);
Vue.use(VueI18n);

const app = document.getElementById('app');

const initialPage = JSON.parse(app.dataset.page);

const i18n = new VueI18n({
    locale: initialPage.props.locale,
    fallbackLocale: initialPage.props.fallbackLocale,
    formatFallbackMessages: true,
    silentTranslationWarn: true,
    silentFallbackWarn: true,
    messages: {
        da
    }
});

new Vue({
    i18n,

    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage,
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
