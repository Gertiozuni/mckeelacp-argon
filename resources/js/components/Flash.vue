<template>
    <div :class="`alert alert-${level} alert-flash`" role="alert" v-show="show">
        {{ body }}
    </div>
</template>

<script>
    export default {
        props: {
            session: {
                type: Object
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
            console.log(this.session)
            if (this.session) {
                this.flash(this.session.message, this.session.level);
            }

            window.events.$on(
                'flash', session => this.flash(session.message, session.level)
            );
        },

        methods: {
            flash(message, level) {
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