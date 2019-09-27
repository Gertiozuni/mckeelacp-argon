/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

window.Vue = require('vue');

/*
* Globel Components
*/
Vue.component('card', require('./components/Card.vue').default);
Vue.component('flash', require('./components/Flash.vue').default);
Vue.component('pagination', require('./components/Pagination/Pagination.vue'));
Vue.component('tabs', require('./components/Tabs/Tabs.vue').default);
Vue.component('tab-pane', require('./components/Tabs/TabPane.vue').default);
Vue.component('vue-badge', require('./components/Badge.vue').default);
Vue.component('vue-modal', require('./components/Modal.vue').default);
Vue.component('base-button', require('./components/BaseButton.vue').default);
Vue.component('base-table', require('./components/BaseTable.vue').default);


/*
* Pages
*/

// users, roles and permissions
Vue.component('permissions-view', require('./components/Pages/Permissions.vue').default);
Vue.component('users-view', require('./components/Pages/Users.vue').default);
Vue.component('roles-view', require('./components/Pages/Roles.vue').default);
Vue.component('role-view', require('./components/Pages/Role.vue').default);

// apple classroom 
Vue.component('appleclassroom-view', require('./components/Pages/AppleClassroom/Index.vue').default);

// cisco
Vue.component('ciscosearch-view', require('./components/Pages/Cisco/Search.vue').default);

// Campuses 
Vue.component('campus-view', require('./components/Pages/Campus/Index.vue').default);

// network
Vue.component('vlans-view', require('./components/Pages/Network/Vlans.vue').default);

Vue.component('switches-view', require('./components/Pages/Network/Switches.vue').default);
Vue.component('switchform-view', require('./components/Pages/Network/SwitchForm.vue').default);
Vue.component('switch-view', require('./components/Pages/Network/Switch.vue').default);
Vue.component('switch-logs-view', require('./components/Pages/Network/SwitchLogs.vue').default);

Vue.component('port-logs-view', require('./components/Pages/Network/PortLogs.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#app',
});
