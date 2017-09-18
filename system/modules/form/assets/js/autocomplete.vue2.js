Vue.component('autocomplete', {
   template: '<div :class="classPrefix" v-on:mousedown="mousefocus = true" v-on:mouseout="mousefocus = false"> \
        <input type="text" v-on:blur="focused = false" v-on:focus="focused = true" \
            v-model="search" :placeholder="placeholder" :class="inputClass" \
            v-on:keydown.down.prevent.stop="moveDown()" \
            v-on:keydown.up.prevent.stop="moveUp()" \
            v-on:keydown.enter.prevent.stop="select(selectedIndex)" \
            v-on:keydown.tab="mousefocus = false" \
            :value="value" \
            ref="input" \
            :required="required"> \
        <div v-if="showSuggestions" :class="classPrefix + \'__suggestions\'"> \
            <div v-for="(entry, index) in filteredEntries" v-on:click="select(index)" :class="[classPrefix + \'__entry\', selectedClass(index)]"> \
                {{ entry[property] }} \
            </div> \
        </div> \
    </div>',
    data: function() {
        return {
            search: '',
            focused: false,
            mousefocus: false,
            selectedIndex: 0
        };
    },
    computed: {
        filteredEntries: function() {
            if (this.search.length <= this.threshold) {
                return [];
            } else {
                var _this = this;
                return this.entries.filter(function(entry) {
                    if (_this.ignoreCase) {
                        return entry[_this.property].toLowerCase().indexOf(_this.search.toLowerCase()) > -1;
                    }
                    return entry[_this.property].indexOf(_this.search) > -1;
                });
            }
        },
        hasSuggestions: function() {
            if (this.search.length <= this.threshold) {
                return false;
            }
            return this.filteredEntries.length > 0;
        },
        showSuggestions: function() {
            if (!this.hasSuggestions) {
                return false;
            }
            if (this.focused || this.mousefocus) {
                return true;
            }
            return false;
        }
    },
    created: function() {
        this.search = this.value;
        if (this.list !== undefined) {
            this.setEntries(this.list);
        } else if (this.url !== undefined && this.requestType !== undefined) {
            this.getListAjax();
        }
    },
    methods: {
        select: function(index) {
            if (this.hasSuggestions) {
                this.search = this.filteredEntries[index][this.property];
                this.$emit('autocomplete-select', this.search);
                if (this.autoHide) {
                    this.mousefocus = false;
                    this.focused = false;
                    this.$refs.input.blur();
                } else {
                    this.$nextTick(function()    {
                        this.$refs.input.focus();
                    });
                }
            }
        },
        setEntries: function(list) {
            this.entries = list;
        },
        moveUp: function() {
            if ((this.selectedIndex - 1) < 0) {
                this.selectedIndex = this.filteredEntries.length - 1;
            } else {
                this.selectedIndex -= 1;
            }
        },
        moveDown: function() {
            if ((this.selectedIndex + 1) > (this.filteredEntries.length - 1)) {
                this.selectedIndex = 0;
            } else {
                this.selectedIndex += 1;
            }
        },
        selectedClass: function(index) {
            if (index === this.selectedIndex) {
                return this.classPrefix + '__selected';
            }
            return '';
        },
        getListAjax: function() {
            var _this = this;
            return this.$http[this.requestType](this.url).then(function(response) {
                _this.setEntries(response.data);
            });
        }
    },
    props: {
        classPrefix: {
            type: String,
            required: false,
            default: 'autocomplete',
        },
        url: {
            type: String,
            required: false,
        },
        requestType: {
            type: String,
            required: false,
            default: 'get',
        },
        list: {
            type: Array,
            required: false,
        },
        placeholder: {
            type: String,
            required: false,
        },
        property: {
            type: String,
            required: false,
            default: 'name',
        },
        inputClass: {
            type: String,
            required: false,
        },
        required: {
            type: Boolean,
            required: false,
            default: false,
        },
        ignoreCase: {
            type: Boolean,
            required: false,
            default: true,
        },
        threshold: {
            type: Number,
            required: false,
            default: 0,
        },
        value: {
            required: false,
            default: '',
        },
        autoHide: {
            type: Boolean,
            required: false,
            default: true,
        }
    },
    watch: {
        filteredEntries: function(value) {
            if (this.selectedIndex > value.length - 1) {
                this.selectedIndex = 0;
            }
        },
        search: function(value) {
            this.$emit('input', value);
        },
        value: function(newValue) {
            this.search = newValue;
        }
    }
});