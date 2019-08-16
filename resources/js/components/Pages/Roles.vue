<script>
    import pagination from '../Pagination/Pagination.vue'

    export default {

        components: {
            pagination
        },

        data() {
            return {
                roles: [],
                pagination: {}
            }
        },

        created() {
            this.getRoles()
        },

        methods: {
            getRoles(page = 1) {
                axios.get(`/roles?page=${page}`).then( ({data}) => {
                    this.roles = data.roles.data
                    this.pagination = data.roles
                })
            },

    		deleteRole(id) {
                axios.delete(`/roles/${id}`).then( ({data}) => {
                    const index = this.roles.findIndex(p => p.id === id)
                    this.$delete(this.roles, index)

                    flash(`${data.role.name} role has been successfully deleted`, 'success' )
                }).catch( ({response}) => {
                    flash( response.data.message, 'error' )
                })
            }
        }
    }
</script>