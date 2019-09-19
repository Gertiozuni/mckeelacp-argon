<script>
    import Multiselect from 'vue-multiselect'

    export default {

        components: {
            Multiselect
        },

        props: {
            switch: {},

            campuses: {
                type: Array
            }
        },

        data() {
            return {
                value: [],
                select: [],
            }
        },

        created() {
            if( this.switch.id ) {

                const school = this.campuses.find(c => c.id == this.switch.campus_id)
                this.value = {
                    label: school.name,
                    id:  school.id
                }
            }

            for(const perm of this.campuses) {
                this.select.push({
                    label: perm.name,
                    id: perm.id
                })
            }
        }


    }
</script>
