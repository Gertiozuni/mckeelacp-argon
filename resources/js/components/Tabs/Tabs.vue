<template>
    <div ref="test"> 
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                <li class="nav-item" v-for="tab in tabs" :key="tab.id" @click="toggleActive( tab )">
                    <a class="nav-link mb-sm-3 mb-md-0" :class="{ 'active': tab.isActive }">{{ tab.name }}</a>
                </li>
            </ul>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade" :class="{ 'show active': tab.isActive }" v-for="tab in tabs" :key="tab.id">
                        {{ tab.id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array
            }
        },

        data() {
            return {
                tabs: [],
                switches: []
            }
        },

        mounted() {
            this.items.forEach(item => {
                item.isActive = false
                this.tabs.push(item)
            })
        },

        methods: {
            toggleActive(tab) {
                this.tabs.forEach(t => {
                    t.isActive = (t.id === tab.id)
                })
            }
        }

    }
</script>

<style>

    .nav-pills .nav-link:hover {
        color: white
    }
</style>

