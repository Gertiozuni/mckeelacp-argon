<script>
    import pagination from '../../Pagination/Pagination.vue'

    export default {

        components: {
            pagination
        },

        props: {
            port: {
                type: Object
            },

            logsInit: {
                type: Object
            }
        },

        data() {
            return {
                logs: this.logsInit,
                search: ''
            }
        },

        methods: {
            getLogs(page = 1) {
                axios.get(`/network/port/${this.port.id}/logs?page=${page}&search=${this.search}`).then( ({data}) => {
                    this.logs = data.logs
                })
            }
        }
    }
</script>