// import bootstrap from 'bootstrap'
import * as $ from 'jquery';
// Object.assign(window, { $ });

import { Modal } from 'bootstrap'; // 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Tab interactions
import { TabAdaptation } from './adaptations/tabs';
import { DropdownAdaptation } from './adaptations/dropdown';

function openModal(url: string) {
    const modal = new Modal(document.getElementById('cmfive-modal')) //, options

    // let xhttp = new XMLHttpRequest();
    // xhttp.onreadystatechange = () => {
    //     if (xhttp.readyState === XMLHttpRequest.DONE && xhttp.status == 200) { 
    //         document.querySelector('#cmfive-modal .modal-content').innerHTML = xhttp.responseText;
    //         modal.show();
    //     }
    // }

    // xhttp.open('GET', url)
    // xhttp.send();

    // $.get(url).done((response) => {
    //     $('#cmfive-modal .modal-content').html(response);
    //     modal.show();
    // })


    fetch(url).then((response) => {
        // console.log(response);
        return response.text()
    }).then((content) => {
        // console.log(content);
        // @todo: find a way to not need query for modals to work
        $('#cmfive-modal .modal-content').html(content);
        // document.querySelector('#cmfive-modal .modal-content').innerHTML = content;
        modal.show();
    })
}

// import CmfiveNav from './components/Nav';
import { Vue } from 'vue-property-decorator';

const THEME_KEY = 'theme';
const app = new Vue({
    el: '#app',
    data: {
        showSidemenu: false,
        containerClass: 'container'
    },
    // components: {CmfiveNav},
    methods: {
        toggleMenu() {
            this.showSidemenu = !this.showSidemenu
        },
        toggleWidth() {
            if (this.containerClass == 'container') {
                this.containerClass = 'container-fluid'
            } else {
                this.containerClass = 'container'
            }
        },
        toggleTheme() {
            // debugger;
            const theme = localStorage.getItem(THEME_KEY);
            if (!theme) {
                localStorage.setItem(THEME_KEY, 'dark');
            } else {
                if (theme === 'default') {
                    localStorage.setItem(THEME_KEY, 'dark');
                } else {
                    localStorage.setItem(THEME_KEY, 'default');
                }
            }

            document.querySelector('html').classList.remove('theme--default');
            document.querySelector('html').classList.remove('theme--dark');
            document.querySelector('html').classList.add('theme--' + localStorage.getItem(THEME_KEY));
        }
    },
    mounted() {
        TabAdaptation.bindTabInteractions();
        DropdownAdaptation.bindDropdownHover();

        let theme = localStorage.getItem(THEME_KEY)

        if (!theme) {
            const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)"); 
            if (prefersDarkScheme.matches) {
                localStorage.setItem(THEME_KEY, 'dark');
            } else {
                localStorage.setItem(THEME_KEY, 'default');
                theme = 'default';
            }
        }
        
        if (theme === "default") {
            document.querySelector('html').classList.remove('theme--dark');
            document.querySelector('html').classList.add('theme--default');
        }

        document.querySelectorAll('[data-modal-target]').forEach((m: Element) => {
            m.addEventListener('click', () => openModal(m.getAttribute('data-modal-target')));
        })
    }
});
