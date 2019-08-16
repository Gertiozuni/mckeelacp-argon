<script>
    import Multiselect from 'vue-multiselect'

    export default {

        components: {
            Multiselect
        },

        props: {
            role: {
                type: Object
            },

            permissions: {
                type: Array
            }
        },

        data() {
            return {
                value: [],
                select: []
            }
        },

        created() {
            for(const perm of this.permissions) {
                this.select.push(perm.name)
            }

            for(const currentPerm of this.role.permissions ) {
                this.value.push( currentPerm.name )
            }
        },

        methods: {
    		submitRole() {
                axios.post(`/roles/${this.role.id}/permissions`, {
                    permissions: this.value
                }).then( ({headers}) => {
                    window.location.href = headers.location;
                })
            },

            capitalize(string) {
                return string.charAt(0).toUpperCase() + string.slice(1)
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
