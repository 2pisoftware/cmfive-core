/**
 * 
 */

Vue.component('html-tabs', {
    data: function() {
        return {
            tabs: []
        };
    },
    methods: {
        selectTab: function(tab, event) {
            event.preventDefault()
            
            for(var i in this.tabs) {
                this.tabs[i].isActive = (tab.title === this.tabs[i].title);
            }
        }
    },
    template:   '<div class="tabs"> \
                    <ul> \
                        <li v-for="tab in tabs" :class="{\'active_tab\': tab.isActive}" v-on:click="selectTab(tab, $event)"> \
                            <i v-if="tab.icon" class="fas tab-icon" :class="tab.icon"></i> <a :href="tab.href">{{ tab.title }}</a> \
                        </li> \
                    </ul> \
                    <div class="tab-content"> \
                        <slot></slot> \
                    </div> \
                </div>',
    created: function() {
        this.tabs = this.$children;
    }
});