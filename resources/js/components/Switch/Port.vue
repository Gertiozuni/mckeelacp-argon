<template>
<div>
   <div class="{{ port.active && port->fiber === 0 ? 'port-squares active-port' : 'port-squares' }}">
        {{ port.port }}
    </div>

    <div v-if="type == 'bottom'" class="{{ $port.active && $port->fiber === 1 ? 'port-squares active-port' : 'port-squares' }}" id="{{ $switch->id }}">
        {{ port.port }}
    </div>
</div>
</template>



<script>

    export default {

        props: {
            port: {
                type: Array
            },

            type: {
                type: String
            }
        },

        data() {
            return {
                model: []
            };
        },

        computed: {
           
        },

        created() {
            
        },

        methods: {
            
        }
    }
</script>
