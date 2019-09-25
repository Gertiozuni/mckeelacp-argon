<script>
    import pagination from '../../Pagination/Pagination.vue'

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
                search: ''
            }
        },

        methods: {
            getLogs(page = 1) {
                axios.get(`/network/switch/${this.switch.id}/logs?page=${page}&search=${this.search}`).then( ({data}) => {
                    this.logs = data.logs
                })
            }
        }
    }
</script>