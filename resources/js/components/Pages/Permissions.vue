<script>
    import pagination from '../Pagination/Pagination.vue'

    export default {

        components: {
            pagination
        },

        data() {
            return {
                permissions: [],
                pagination: {},
                search: ''
            }
        },

        created() {
            this.getPermissions()
        },

        methods: {
            getPermissions(page = 1) {
                 console.log(this.search)
                axios.get(`/permissions?page=${page}&search=${this.search}`).then( ({data}) => {
                    this.permissions = data.permissions.data
                    this.pagination = data.permissions
                })
            },

    		deletePermission(id, name) {
                axios.delete(`/permissions/${id}`).then( response => response.data ).then( data => {
                    const index = this.permissions.findIndex(p => p.id === id)
                    this.$delete(this.permissions, index)
                })
            }
        }
    }
</script>