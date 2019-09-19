<template>
    <button v-if="icon" 
        :class="`btn ${icon ? 'btn-icon btn-3' : ''} btn-${outline ? outline + '-' : '' }${color} ${getSize()}`" 
        type="button"
        :data-toggle="`${tooltip.title ? 'tooltip' : '' }`" :data-placement="tooltip.placement" :title="tooltip.title"
    >
	    <span v-if="icon" class="btn-inner--icon"><i :class="`${icon}`"></i></span>
	
        <span v-if="icon && text" class="btn-inner--text">{{ text }}</span>

        {{ !icon ? text : '' }}
    </button>
</template>

<script>
export default {

    props: {
        text: {
            required: false,
            type: String
        },

        icon: {
            required: false,
            type: String
        },

        color: {
            default: 'default',
            type: String,
        },

        size: {
            default: '',
            type: String
        },

        outline: {
            default: false,
            type: Boolean
        },

        disabled: {
            default: false,
            type: Boolean
        },

        tooltip: {
            type: Object,
            default: () => ({
                title: '',
                placement: ''
            })
        }
    },

    methods: {
        getSize() {
            if( this.size == 'small' ) {
                return 'btn-sm'
            }
            else if( this.size == 'large' ) {
                return 'btn-lg'
            }
        
            return false
        }
    }
}
</script>
