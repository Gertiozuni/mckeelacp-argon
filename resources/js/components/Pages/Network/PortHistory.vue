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

            historyProp: {
                type: Object
            }
        },

        data() {
            return {
                histories: this.historyProp,
                search: ''
            }
        },

        methods: {
            getHistories(page = 1) {
                axios.get(`/network/port/${this.port.id}/history?page=${page}&search=${this.search}`).then( ({data}) => {
                    this.histories = data.histories
                })
            }
        }
    }
</script>