<template>
    <div class="tabs">
        <ul>
            <li v-for="tab in tabs" :class="{'active_tab': tab.isActive}" :key="tab.href" @click.prevent="selectTab(tab)">
                <i v-if="tab.icon" class="fas tab-icon" :class="tab.icon"></i> <a :href="tab.href">{{ tab.title }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <slot></slot>
        </div>
    </div>
</template>

<script lang="ts">
    import { Vue, Component } from 'vue-property-decorator';

    @Component
    export default class extends Vue {
        tabs: Array<any> = [];

        selectTab(tab) {
            this.tabs.map((t: any) => t.isActive = (tab.title === t.title));
        }

        created() {
            this.tabs = this.$children;
        }
    }
</script>

<style lang="scss" scoped>

$tab-border-colour: #d3d7dc;
$tab-background-colour: #bbc9d4;

$tab-content-border-colour: #d3d7dc;
$tab-content-background-colour: #fff;

div.tabs {

	& > ul {
		display: block;
		clear: both;
		height: auto;
		margin: 0px;

		& > li {
			display: inline-block;
			// float: left;
			list-style-type: none;
			background-color: $tab-background-colour;
			color: #93aeb9;
			margin-right: 5px;
			padding: 5px 20px;
			border-top: 1px solid $tab-border-colour;
			border-left: 1px solid $tab-border-colour;
			border-right: 1px solid $tab-border-colour;
			cursor: pointer;

			.tab-icon {
				margin-right: 5px;
			}

			a {
				color: black;
			}

			&.active_tab {
				background-color: white;
				padding: 10px 20px 5px;
				border-bottom: 1px solid white;
				margin-bottom: -1px;
			}
		}
	}

	div.tab-content {
		padding: 20px;
		// margin-top: 0px;
		background-color: $tab-content-background-colour;
		display: block;
		border: 1px solid $tab-content-border-colour;
	}
}
</style>
