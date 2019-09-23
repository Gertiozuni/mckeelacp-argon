<script>
    import Multiselect from 'vue-multiselect'

    export default {

        components: {
            Multiselect
        },

        props: {
            networkSwitch: {
                type: Object
            },

            vlans: {
                type: Array
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
                ports: this.networkSwitch.ports,
                modal: {
                    active: false,
                    disabled: false,
                    type: ''
                },
                mode: {
                    port: null,
                    vlans: null,
                    mode: null,
                    active: false
                },
                vlan: {
                    vlans: null,
                    mode: null
                },
                activePort: null,
                modes: [ 'access', 'general' ],
                vlansList: this.vlans,
                taggedCheck: true,
                configCheck: true,
                modalPort: null
            }
        },

        created() {
            console.log(this.vlansList)
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
            },

            toggleModal(type, port) {
                // show the modal
                this.modal.type = type
                this.modal.active = true
                this.activePort = port

                if(type == 'mode') {
                    this.mode.port = port.port
                    this.mode.mode = port.mode
                    this.mode.vlans = port.vlans
                }
                else {
                    this.vlan.mode = port.mode
                    this.vlan.vlans = port.vlans
                }
            },

            hideModal() {
                this.modal.type = null
                this.modal.active = false
                this.modal.disabled = false

                this.mode.port = null
                this.mode.mode = null
                this.mode.vlans = null
                this.mode.active = null

                this.activePort = null

                this.vlan.mode = null
                this.vlan.vlans = null
            },

            selectVlans() {
                if( this.mode.mode == 'general') {
                    this.mode.vlans = this.vlans
                }
                else {
                    this.mode.vlans = this.activePort.vlans
                }

                if( this.mode.mode != this.activePort.mode ) {
                    this.modal.disabled = false
                }
                else {
                    this.modal.disabled = true
                }
            },

            submitModeChange() {
                /* find the port */
                const index = this.searchPorts.findIndex(p => p.id === this.activePort.id)
                const port = this.searchPorts[index]

                if( ! this.modal.disabled ) {
                    axios.patch( `/network/port/${port.id}/mode`, {
                        'mode': this.mode.mode,
                        'vlans': this.mode.vlans,
                        'saveConfig': this.configCheck,
                        'tagged': this.taggedCheck
                    }).then( ({data}) => {
                        this.searchPorts[index].mode = this.mode.mode
                        this.searchPorts[index].vlans = data.port.vlans
                        flash(`Port Mode has been successfully updated`, 'success' )
                        this.hideModal()
                        this.$forceUpdate()
                    })
                }
            },

            submitVlansChange() {
                /* find the port */
                const index = this.searchPorts.findIndex(p => p.id === this.activePort.id)
                const port = this.searchPorts[index]

                if( ! this.modal.disabled ) {
                    axios.patch( `/network/port/${port.id}/vlans`, {
                        'mode': this.vlan.mode,
                        'vlans': this.vlan.vlans,
                        'saveConfig': this.configCheck,
                        'tagged': this.taggedCheck
                    }).then( ({data}) => {
                        this.searchPorts[index].vlans = data.port.vlans
                        flash(`Port vlans has been successfully updated`, 'success' )
                        this.hideModal()
                        this.$forceUpdate()
                    })
                }
            }
        }
    }
</script>
<style>

.description{
    cursor: pointer;
}
</style>