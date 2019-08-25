<script>
    import Multiselect from 'vue-multiselect'

    export default {

        components: {
            Multiselect
        },

        props: {
            campuses: {
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
            for(const perm of this.campuses) {
                this.select.push({
                    label: perm.name,
                    id: perm.id
                })
            }
        }


    }
</script>
