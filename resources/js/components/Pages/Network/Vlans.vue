<script>
    export default {

        props: {
            vlans: {
                type: Array
            }
        },

        data() {
            return {
                search: ''
            }
        },

        computed: {
            filteredVlans() {
                return this.vlans.filter(vlan => {
                    return ( vlan.vlan.toString().includes( this.search.toLowerCase() ) || vlan.description.toLowerCase().includes(this.search.toLowerCase() ))
                })
            }
        },

        methods: {
    		deleteVlan(vlan) {
                axios.delete(`/network/vlans/${vlan.id}`).then( response => response.data ).then( data => {
                    let index = this.vlans.findIndex(v => v === vlan)
                    this.$delete(this.vlans, index)

                    index = this.filteredVlans.findIndex(v => v === vlan)
                    this.$delete(this.filteredVlans, index)
                    this.$forceUpdate();

                    flash(`${vlan.name} has been successfully deleted`, 'success' )
                })
            }
        }
    }
</script>