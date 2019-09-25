window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.Vue = require('vue');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 *  Moment
 */
import moment from 'moment'
Vue.prototype.moment = moment


window.events = new Vue();

/* flash event */
window.flash = function (message, level = 'success') {
    window.events.$emit('flash', { message, level });
};

/* truncate */
Vue.filter('truncate', function (text, length, suffix) {
    if( text.length > length ) {
        return text.substring(0, length) + suffix
    }
    return text
})

/* date picker */
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
Vue.use(VueFlatPickr);

/* scroll event */
window.scrollToDiv = (location) => {
    $(location).stop().animate({
        scrollTop: 0
    }, 'slow', 'swing')
}