var TwoPiPagination = {
	data: function() {
        return {
            current_page: 1,
            first_page: 1,
            last_page: 5
        }
	},
  
	props: {
        data_count: {
            required: true
        },
        
        chunk_size: {
            type: Number,
            default: 5
        },

        items_per_page: {
            type: Number,
            default: 10
        }
    },

    methods: {
        setCurrentPage: function(page) { 
            if (this.current_page !== page) this.current_page = page;
        },

        nextPage: function() {
            if ((this.current_page * this.items_per_page) < this.data_count) this.current_page++;
            if (this.current_page > this.last_page) {
                this.first_page += this.chunk_size;
                this.last_page += this.chunk_size;
                this.current_page = this.first_page;
            }
        },

        prevPage: function() {
            if (this.current_page > 1) this.current_page--;
            if (this.current_page < this.first_page) {
                this.first_page -= this.chunk_size;
                this.last_page = this.first_page + this.chunk_size - 1;
            }
        },

        firstPage: function() {
            this.first_page = 1;
            this.last_page = this.chunk_size;
            if (this.current_page > 1) this.current_page = 1;
        },

        lastPage: function() {
            var chunk_count = Math.floor(this.page_count / this.chunk_size);
            this.first_page = chunk_count * this.chunk_size + 1;
            this.last_page = this.page_count;
            if (this.current_page < this.page_count) this.current_page = this.page_count;
        },

        next_chunk: function() {
            if (this.last_page >= this.page_count) return;
            
            this.first_page += this.chunk_size;
            this.last_page += this.chunk_size;
            this.current_page = this.first_page;
        },

        prev_chunk: function() {
            if (this.current_page === 1) return;

            this.first_page -= this.chunk_size;
            this.last_page = this.first_page + this.chunk_size - 1;
            this.current_page = this.first_page;
        }
    },

    watch: {
        current_page: function() {
            this.$emit('currentpagechanged', { start: this.start, end: this.end } );
        }
    },
    
    computed: {
        page_count: function() { 
            return Math.ceil(this.data_count / this.items_per_page);
        },

        pages: function() {
            var p = [];
            for (var i = this.first_page; i < this.last_page + 1; i++) p.push(i);
            return p;
        },

        start: function() {
            return (this.current_page - 1) * this.items_per_page;
        },

        end: function() {
            return this.current_page * this.items_per_page;
        }
    },
  
    template: `
        <div class='row-fluid text-center' v-if="page_count > 1">
            <br>
            <button class="button tiny radius" @click="firstPage">First page</button>
            <button class="button tiny radius" @click="prev_chunk"><i class="fas fa-angle-double-left"></i></button>
            <button class="button tiny radius" @click="prevPage"><i class="fas fa-angle-left"></i></button>

            <button :class="{button: true, tiny: true, radius: true, success: current_page === n}" v-for="n in pages" v-if="n <= page_count" @click="setCurrentPage(n)">{{n}}</button>

            <button class="button tiny radius" @click="nextPage"><i class="fas fa-angle-right"></i></button>
            <button class="button tiny radius" @click="next_chunk"><i class="fas fa-angle-double-right"></i></button>
            <button class="button tiny radius" @click="lastPage">Last page</button>
            <br><br>
            page {{this.current_page}} of {{this.page_count}}
        </div>
    `
};