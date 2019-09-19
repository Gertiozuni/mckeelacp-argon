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

        data() {
            return {
                search: '',
                ports: []
            }
        },

        created() {
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

            deletePort(port) {

            }
        }
    }
</script>
