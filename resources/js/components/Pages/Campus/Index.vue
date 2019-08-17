<script>
    export default {

    	props: {
    		campusesprop: {
    			type: Array
    		}
    	},

	    data() {
	        return {
	            campuses: this.campusesprop,
	        }
   		},


        methods: {
    		deleteCampus(id) {
                axios.delete(`/campuses/${id}`).then( ({data}) => {
                    const index = this.campuses.findIndex(p => p.id === id)
                    this.$delete(this.campuses, index)

                    flash(`${data.campus.name} has been successfully deleted`, 'success' )
                }).catch( ({response}) => {
                    flash( response.data.message, 'danger' )
                })
            },
        }
    }
</script>