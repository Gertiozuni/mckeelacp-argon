<script>
    import pagination from '../../Pagination/Pagination.vue'
    import flatPickr from 'vue-flatpickr-component';
    import 'flatpickr/dist/flatpickr.css';

    export default {

        components: {
            pagination
        },

        props: {
            switch: {
                type: Object
            },

            logsInit: {
                type: Object
            }
        },

        data() {
            return {
                logs: this.logsInit,
                event: '',
                startDate: null,
                endDate: null,
                port: null
            }
        },

        methods: {
            getLogs(page = 1) {
                axios.get(`/network/switch/${this.switch.id}/logs`, {
                    params: {
                        page: page,
                        event: this.event,
                        startDate: this.startDate,
                        endDate: this.endDate,
                        port: this.port
                    }
                }).then( ({data}) => {
                    this.logs = data.logs
                })
            }
        }
    }
</script>