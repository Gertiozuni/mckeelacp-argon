<template>
<div>
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="mb-0">{{ networkSwitch.model }}</h3>
            </div>
        </div>
    </div>
                    
    <div class="col-12">
    </div>

	<slot name="topports"></slot>

	<slot name="bottomports"></slot>

</div>
</template>



<script>

    export default {

        props: {
            networkSwitch: {
                type: Array
            }
        },

        data() {
            return {
                model: [],
                search: ''
            };
        },

        computed: {
            isOdd(number) {
                return number % 2 === 0
            }
        },

        created() {
            
        },

        methods: {
        }
    }
</script>
