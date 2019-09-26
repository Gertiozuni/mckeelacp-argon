<script>
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import vue2Dropzone from 'vue2-dropzone'

export default {

    components: {
        dropzone: vue2Dropzone
    },

    data() {
        return {
            token: document.head.querySelector(`meta[name='csrf-token']`).content,
        }
    },

    methods: {

        upload() {
            this.$refs.myVueDropzone.processQueue()
        },

        updateClassroom() {
            this.$refs.myVueDropzone.removeAllFiles()

            flash( 'Files successfully updated' );

            // with our uploaded files, do the magic
            axios.post(`/appleclassroom/update`).then( ({data}) => {                
                flash( `Apple classroom has been updated. Email sent to ${data.user.email}`, 'success' )
            }).catch( error => {
                flash( `There was a problem. Could not update`, 'danger' )
            })
        },

        uploadError(file, message, xhr) {
            flash( message, 'danger' )
        }
    }
}

</script>

<style>
#dropzone {
    background-color: #272c47;
    font-family: 'Arial', sans-serif;
    letter-spacing: 0.2px;
    color: #777;
    transition: background-color .2s linear;
    border-color: #272c47;
}

.vue-dropzone>.dz-preview .dz-details {
    background-color: #5854ba;
}
</style>
