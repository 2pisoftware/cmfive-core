// src/app.ts

import { Modal } from 'bootstrap';
// import CmfiveNav from './components/Nav';
// import { AccordionAdaptation } from './adaptations/accordion';
import { AlertAdaptation } from './adaptations/alert';
import { DropdownAdaptation } from './adaptations/dropdown';
import { FavouritesAdaptation} from './adaptations/favourites';
import { TabAdaptation } from './adaptations/tabs';
import { TableAdaptation } from './adaptations/table';
import { Toast } from './components/Toast';
import { QuillEditor } from './components/QuillEditor';

function openModal(url: string) {
    const modal = new Modal(document.getElementById('cmfive-modal')) //, options

    let modalContent = document.querySelector('#cmfive-modal .modal-content');
    if (modalContent) {
        modalContent.innerHTML = '';
    }

    modal.show();
    fetch(url, {
        headers: {
            'Content-Type': 'application/json'
        }
    }).then((response) => {
        return response.text()
    }).then((content) => {
        modalContent.innerHTML = content;

        // Rebind elements for modal
        Cmfive.ready(document.getElementById('#cmfive-modal'));
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

    private static modalClickListener = function() {
        if (this.hasAttribute('data-modal-confirm')) {
            if (confirm(this.getAttribute('data-modal-confirm'))) {
                openModal(this.getAttribute('data-modal-target'));
            }
        } else {
            openModal(this.getAttribute('data-modal-target'))
        }
    }

    private static menuOpenClickListener = function() {
        if (!document.getElementById('menu-overlay').classList.contains('active')) {
            document.getElementById('menu-overlay').classList.add('active');
        }
        if (!document.getElementById('offscreen-menu').classList.contains('active')) {
            document.getElementById('offscreen-menu').classList.add('active');
        }
    }

    private static menuCloseClickListener = function() {
        if (document.getElementById('menu-overlay').classList.contains('active')) {
            document.getElementById('menu-overlay').classList.remove('active');
        }
        if (document.getElementById('offscreen-menu').classList.contains('active')) {
            document.getElementById('offscreen-menu').classList.remove('active');
        }
    }

    /**
     * Ready can be called on a target (like a modal) to bind interactions on elements
     * that are loaded dynamically onto the page
     * 
     * @param target Document|Element
     */
    static ready(target: Document|Element) {
        // AccordionAdaptation.bindAccordionInteractions();
        AlertAdaptation.bindCloseEvent();
        DropdownAdaptation.bindDropdownHover();
        FavouritesAdaptation.bindFavouriteInteractions();
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
        target?.querySelectorAll('[data-modal-target]')?.forEach((m: Element) => {
            m.removeEventListener('click', Cmfive.modalClickListener);
            m.addEventListener('click', Cmfive.modalClickListener);
        })

        // Theme toggle
        target?.querySelectorAll('[data-toggle-theme]')?.forEach(t => {
            t.removeEventListener('click', Cmfive.toggleTheme);
            t.addEventListener('click', Cmfive.toggleTheme);
        })

        // Menu toggle
        target?.querySelectorAll('[data-toggle-menu="open"]')?.forEach(m => {
            m.removeEventListener('click', Cmfive.menuOpenClickListener);
            m.addEventListener('click', Cmfive.menuOpenClickListener);
        });

        target?.querySelectorAll('[data-toggle-menu="close"]')?.forEach(m => {
            m.removeEventListener('click', Cmfive.menuCloseClickListener);
            m.addEventListener('click', Cmfive.menuCloseClickListener);
        });
    }
}

window.addEventListener('load', () => Cmfive.ready(document));
