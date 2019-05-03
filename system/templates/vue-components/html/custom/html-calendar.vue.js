/**
 * Requires jquery, clndr.js and moment.js to be included
 */
Vue.component('html-calendar', {
	props: {
		preventPast: {
			type: Boolean,
			required: false,
			default: false
		},
		preventFuture: {
			type: Boolean,
			required: false,
			default: false
		},
		selectedDate: {
			type: Date,
			required: false
		}
	},
	data: function() {
		return {
			days: {},
			month: moment().startOf('month'),
			current_month: moment().startOf('month')
		}
	},
	methods: {
		changeMonth: function(amount) {
			if (amount < 0) {
				if (this.preventPast == false || this.current_month.isBefore(this.month)) {
					this.month = moment(this.month).add(amount, 'month')
				}
			} else {
				if (this.preventFuture == false || this.current_month.isAfter(this.month)) {
					this.month = moment(this.month).add(amount, 'month')
				}
			}

			this.getDays()
		},
		getDays: function() {
			var start_date = moment(this.month).startOf("month")
			this.days = []
			
			for(i = 1; i < start_date.isoWeekday(); i++) {
				this.days.push({day: '&nbsp;', classes: 'day disabled', obj: null})
			} 
			
			for(i = 0; i < this.month.daysInMonth(); i++) {
				var today = (moment().startOf('day').isSame(start_date))
				var disabled = (this.preventPast === true && start_date.isSameOrBefore(moment().startOf('day')))
				
				var selected_date = this.selectedDate ? moment(this.selectedDate).startOf('day').isSame(start_date) : false;

				this.days.push({day: start_date.date(), classes: 'day' + (today ? ' today' : '') + (disabled ? ' disabled' : '') + (selected_date ? ' selected' : ''), obj: start_date.clone()})
				start_date = start_date.add(1, 'day')
			}
		},
		emitClick: function(event) {
			this.$emit('click', event);
		}
	},
	computed: {
		can_go_back: function() {
			if (this.preventPast === true && this.month.isSame(this.current_month)) {
				return 'disabled'
			}
		},
		days_of_week: function() {
			return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
		}
	},
	created: function() {
		this.getDays()
	},
	template:   '<div class="cmfive-calendar"> \
					<div class="cmfive-calendar-controls"> \
					    <div class="cmfive-calendar-previous-button" @click="changeMonth(-1)" :class="can_go_back">&lsaquo;</div> \
					    <div class="month">{{ month.format(\'MMMM YYYY\') }}</div> \
					    <div class="cmfive-calendar-next-button" @click="changeMonth(1)">&rsaquo;</div> \
					</div> \
					<div class="cmfive-calendar-grid"> \
					    <div class="days-of-the-week"> \
					    	<div class="week-days" v-for="_day in days_of_week" v-html="_day"></div> \
				    	</div> \
						<div class="days"> \
					        <div v-for="(day, index) in days" :key="index" :class="day.classes" v-html="day.day" @click.prevent="emitClick(day.obj)"></div> \
				        </div> \
					</div> \
				</div>'
})