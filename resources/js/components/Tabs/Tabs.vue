<template>
    <div>
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                <li class="nav-item" v-for="tab in tabs" :key="tab.id">
                    <a :href="tab.href" class="nav-link mb-sm-3 mb-md-0" :class="{ 'active': tab.isActive }" @click="selectTab(tab)">{{ tab.name }}</a>
                </li>
            </ul>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>



<script>

    export default {

        data() {
            return {
                tabs: []
            };
        },

        created() {
            this.tabs = this.$children;
        },

        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.href == selectedTab.href)
                })
            }
        }
    }
</script>
