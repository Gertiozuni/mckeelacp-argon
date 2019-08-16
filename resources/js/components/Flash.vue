<template>
    <div :class="`alert alert-${level} alert-flash`" role="alert" v-show="show">
        {{ body }}
    </div>
</template>

<script>
    export default {
        props: {
            session: {
                type: Array
            }
        },

        data() {
            return {
                body: '',
                show: false,
                level: 'success'
            }
        },

        created() {
            if (this.session[0]) {
                console.log(this.session[0][0])
                this.flash(this.session[0][0].message, this.session[0][0].level);
            }

            window.events.$on(
                'flash', session => this.flash(session[0][0].message, session[0][0].level)
            );
        },

        methods: {
            flash(message, level) {
                console.log( level, message)
                this.body = message;
                this.level = level
                this.show = true;

                this.hide();
            },

            hide() {
                setTimeout(() => {
                    this.show = false;
                }, 3000);
            }
        }
    };
</script>
<!-- 
<style>
    .alert-flash {
        position: fixed;
        right: 25px;
        bottom: 25px;
    }
</style> -->