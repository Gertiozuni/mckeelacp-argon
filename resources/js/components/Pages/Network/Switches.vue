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
            }
        }
    }
</script>
