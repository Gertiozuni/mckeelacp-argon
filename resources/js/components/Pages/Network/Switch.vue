<script>
    import Multiselect from 'vue-multiselect'

    export default {

        components: {
            Multiselect
        },

        props: {
            networkSwitch: {
                type: Object
            }
        },

        computed: {
            searchPorts() {
                return this.ports.filter(port => {
                    return ( port.port.toString().includes( this.search.toLowerCase() ) || ( port.description && port.description.toLowerCase().includes(this.search.toLowerCase() ) ))
                })
            }
        },

        data() {
            return {
                search: '',
                tempValue: null,
                ports: this.networkSwitch.ports
            }
        },

        created() {
            console.log( this.networkSwitch.fiber_ports )
        },

        methods: {
            getVlans(vlans) {
                let data = []
                if(vlans.length > 0) {
                    for( let vlan of vlans ) {
                        data.push( vlan.vlan )
                    }
                }
                return data
            },

            enableEdit(port) {
                this.tempValue = port.description;
                port.editing = true
                this.$forceUpdate()

                this.$nextTick(() => {
                    this.$refs.edit[0].focus()
                })
            },

            disableEdit(port) {
                axios.patch(`/network/port/${port.id}`, {
                    'description': this.tempValue
                }).then( ({data}) => {
                    const index = this.ports.findIndex(p => p.id === port.id)
                    this.ports[index].description = this.tempValue
                    this.ports[index].editing = false
                    this.tempValue = null

                    flash(`Port description has been successfully updated`, 'success' )
                })

                
                this.$forceUpdate()
            }
        }
    }
</script>
<style>

.description{
    cursor: pointer;
}
</style>