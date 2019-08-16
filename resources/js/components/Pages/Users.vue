<script>
    import pagination from '../Pagination/Pagination.vue'

    export default {

        components: {
            pagination
        },

        data() {
            return {
                users: [],
                pagination: {}
            }
        },

        created() {
            this.getUsers()
        },

        methods: {
            getUsers(page = 1) {
                axios.get(`/users?page=${page}`).then( ({data}) => {
                    console.log(data)
                    this.users = data.users.data
                    this.pagination = data.users
                })
            },

    		deleteUser(id, name) {
                axios.delete(`/users/${id}`).then( response => response.data ).then( data => {
                    const index = this.users.findIndex(p => p.id === id)
                    this.$delete(this.users, index)

                    flash(`${name} has been successfully deleted`, 'success' )
                })
            },

            capitalize(string) {
                return string.charAt(0).toUpperCase() + string.slice(1)
            }
        }
    }
</script>