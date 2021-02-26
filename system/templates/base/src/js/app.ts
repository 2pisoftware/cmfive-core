// import bootstrap from 'bootstrap'
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Tab interactions
import { TabAdaptation } from './adaptations/tabs';

// import CmfiveNav from './components/Nav';
import { Vue } from 'vue-property-decorator';

const app = new Vue({
    el: '#app',
    data: {
        showSidemenu: false,
    },
    // components: {CmfiveNav},
    methods: {
        toggleMenu() {
            this.showSidemenu = !this.showSidemenu
        }
    },
    mounted() {
        TabAdaptation.bindTabInteractions();
    }
});
