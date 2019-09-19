<template>
    <span :class="`badge ${pill ? 'badge-pill' : '' } badge-${color}`">{{ text }}</span>
</template>

<script>
export default {

    props: {
        text: {
            required: true,
            type: String
        },

        color: {
            default: 'default',
            type: String,
        },

        pill: {
            default: false,
            type: Boolean
        },
    },
}
</script>
