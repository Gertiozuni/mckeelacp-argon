<script>

export default {

    data() {
        return {
            serials: '',
            devices: null
        }
    },

    methods: {

        search() {
            axios.post(`/cisco/search`, {
                serials: this.serials
            }).then( ({data}) => {
                this.devices = data.devices
            })
        },
    }
}

</script>
