<script>
    export default {

        props: {
            campuses: {
                type: Array
            }
        },

        methods: {
            groupBy(items, key) {
                const result = {}

                items.forEach(item => {
                    if (! result[item[key]] ) {
                        result[item[key]] = []
                    }
                    result[item[key]].push(item)
                })

                return result
            },

            deleteSwitch(theSwitch) {
                axios.delete(`/network/switches/${theSwitch.id}`).then(({data}) => {
                    const campus = this.campuses.findIndex(p => p.id === theSwitch.campus_id)
                    const index = this.campuses[campus].switches.findIndex(s => s === theSwitch )
                    this.$delete(this.campuses[campus].switches, index)

                    this.$forceUpdate()

                    this.backToTop()
                    flash(`${theSwitch.ip_address} has been successfully deleted`, 'success' )
                }).catch( ({response}) => {
                    console.log
                    flash( response.data.message, 'danger' )
                })
            },

            backToTop() {
                $('html,body').stop().animate({
                    scrollTop: 0
                }, 'slow', 'swing');
            }
        }
    }
</script>
