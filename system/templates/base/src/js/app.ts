// import bootstrap from 'bootstrap'
// import * as $ from 'jquery';
// import Vue from 'vue';

// Object.assign(window, { Vue, $ });

import { Modal } from 'bootstrap'; // 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import CmfiveNav from './components/Nav';
// import { AccordionAdaptation } from './adaptations/accordion';
import { DropdownAdaptation } from './adaptations/dropdown';
import { TabAdaptation } from './adaptations/tabs';
import { TableAdaptation } from './adaptations/table';
import { QuillEditor } from './components/QuillEditor';

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

    modal.show();
    fetch(url, {
        headers: {
            'Content-Type': 'application/json'
        }
    }).then((response) => {
        return response.text()
    }).then((content) => {
        // @todo: find a way to not need query for modals to work
        $('#cmfive-modal .modal-content').html(content);
        // Rebind elements for modal
        Cmfive.ready();
    })
}


class Cmfive {
    static THEME_KEY = 'theme';

    static toggleTheme() {
        // debugger;
        const theme = localStorage.getItem(Cmfive.THEME_KEY);
        if (!theme) {
            localStorage.setItem(Cmfive.THEME_KEY, 'dark');
        } else {
            if (theme === 'default') {
                localStorage.setItem(Cmfive.THEME_KEY, 'dark');
            } else {
                localStorage.setItem(Cmfive.THEME_KEY, 'default');
            }
        }

        document.querySelector('html').classList.remove('theme--default');
        document.querySelector('html').classList.remove('theme--dark');
        document.querySelector('html').classList.add('theme--' + localStorage.getItem(Cmfive.THEME_KEY));
    }

    static ready() {
        // AccordionAdaptation.bindAccordionInteractions();
        DropdownAdaptation.bindDropdownHover();
        TabAdaptation.bindTabInteractions();
        TableAdaptation.bindTableInteractions();
        QuillEditor.bindQuillEditor();

        let theme = localStorage.getItem(Cmfive.THEME_KEY)

        if (!theme) {
            const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)"); 
            if (prefersDarkScheme.matches) {
                localStorage.setItem(Cmfive.THEME_KEY, 'dark');
            } else {
                localStorage.setItem(Cmfive.THEME_KEY, 'default');
                theme = 'default';
            }
        }
        
        if (theme === "default") {
            document.querySelector('html').classList.remove('theme--dark');
            document.querySelector('html').classList.add('theme--default');
        }

        // Bind modal links
        document.querySelectorAll('[data-modal-target]').forEach((m: Element) => {
            m.addEventListener('click', () => {
                if (m.hasAttribute('data-modal-confirm')) {
                    if (confirm(m.getAttribute('data-modal-confirm'))) {
                        openModal(m.getAttribute('data-modal-target'));
                    }
                } else {
                    openModal(m.getAttribute('data-modal-target'))
                }
            });
        })

        // Theme toggle
        document.querySelectorAll('[data-toggle-theme]')?.forEach(t => {
            t.removeEventListener('click', (event) => Cmfive.toggleTheme());
            t.addEventListener('click', (event) => Cmfive.toggleTheme());
        })

        // Menu toggle
        document.querySelectorAll('[data-toggle-menu="open"]')?.forEach(m => {
            m.addEventListener('click', (event) => {
                console.log(event);
                if (!document.getElementById('menu-overlay').classList.contains('active')) {
                    document.getElementById('menu-overlay').classList.add('active');
                }
                if (!document.getElementById('offscreen-menu').classList.contains('active')) {
                    document.getElementById('offscreen-menu').classList.add('active');
                }
            })
        });

        document.querySelectorAll('[data-toggle-menu="close"]')?.forEach(m => {
            m.addEventListener('click', (event) => {
                console.log(event);
                if (document.getElementById('menu-overlay').classList.contains('active')) {
                    document.getElementById('menu-overlay').classList.remove('active');
                }
                if (document.getElementById('offscreen-menu').classList.contains('active')) {
                    document.getElementById('offscreen-menu').classList.remove('active');
                }
            })
        });
    }
}

window.addEventListener('load', () => Cmfive.ready());
