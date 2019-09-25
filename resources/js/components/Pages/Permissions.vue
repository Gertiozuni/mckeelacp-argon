<script>
    import pagination from '../Pagination/Pagination.vue'

    export default {

        components: {
            pagination
        },

        props: {
            permissionsInit: {
                type: Object
            }
        },

        data() {
            return {
                permissions: this.permissionsInit,
                search: ''
            }
        },

        methods: {
            getPermissions(page = 1) {
                axios.get(`/permissions?page=${page}&search=${this.search}`).then( ({data}) => {
                    this.permissions = data.permissions
                })
            },

    		deletePermission(id, name) {
                axios.delete(`/permissions/${id}`).then( response => response.data ).then( data => {
                    const index = this.permissions.findIndex(p => p.id === id)
                    this.$delete(this.permissions, index)
                    flash(`${name} has been successfully deleted`, 'success' )
                })
            }
        }
    }
</script>