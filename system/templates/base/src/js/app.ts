// src/app.ts
import { AlertAdaptation } from './adaptations/alert';
import { DropdownAdaptation } from './adaptations/dropdown';
import { FavouritesAdaptation} from './adaptations/favourites';
import { TabAdaptation } from './adaptations/tabs';
import { TableAdaptation } from './adaptations/table';
import { QuillEditor } from './components/QuillEditor';
import { InputWithOther } from './components/InputWithOther';

import { Modal } from 'bootstrap';
import { MultiFileUpload } from './components/MultiFileUpload';

// import { CmfiveHelper } from './CmfiveHelper';

class Cmfive {
    static THEME_KEY = 'theme';
    static currentModal: Modal;

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

    static menuOpenClickListener = function() {
        if (!document.getElementById('menu-overlay').classList.contains('active')) {
            document.getElementById('menu-overlay').classList.add('active');
        }
        if (!document.getElementById('offscreen-menu').classList.contains('active')) {
            document.getElementById('offscreen-menu').classList.add('active');
        }
    }

    static menuCloseClickListener = function() {
        if (document.getElementById('menu-overlay').classList.contains('active')) {
            document.getElementById('menu-overlay').classList.remove('active');
        }
        if (document.getElementById('offscreen-menu').classList.contains('active')) {
            document.getElementById('offscreen-menu').classList.remove('active');
        }
    }

    static modalClickListener = function() {
        if (this.hasAttribute('data-modal-confirm')) {
            if (!confirm(this.getAttribute('data-modal-confirm'))) {
                return false;
            }
        }

        Cmfive.openModal(this.getAttribute('data-modal-target'))
    }

    static linkClickListener = function() {
        if (this.hasAttribute('data-link-confirm')) {
            if (!confirm(this.getAttribute('data-link-confirm'))) {
                return false;
            }
        }

        window.location.href = this.getAttribute('data-link-target');
    }

    static openModal(url: string) {
        Cmfive.currentModal = new Modal(document.getElementById('cmfive-modal')) //, options
    
        let modalContent = document.querySelector('#cmfive-modal .modal-content');
        if (modalContent) {
            modalContent.innerHTML = '';
        }
    
        Cmfive.currentModal.show();
        fetch(url, {
            headers: {
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.text()
        }).then((content) => {
            modalContent.innerHTML = content;
    
            // Rebind elements for modal
            Cmfive.ready(modalContent);
        })
    }

    static formCancel()
    {
        if (Cmfive.currentModal) {
            Cmfive.currentModal.hide();
            Cmfive.currentModal = null;
        } else {
            window.history.back();
        }
    }

    /**
     * Ready can be called on a target (like a modal) to bind interactions on elements
     * that are loaded dynamically onto the page
     * 
     * @param target Document|Element
     */
    static ready(target: Document|Element) {
        if (!window.hasOwnProperty('cmfiveEventBus')) {
            // @ts-ignore
            window.cmfiveEventBus = document.createComment('Helper')
            // @ts-ignore
            window.cmfiveEventBus.addEventListener('dom-update', (event) => {
                console.log("DOM EVENT", event); // => detail-data
                Cmfive.ready(event.detail);
            });
        }

        if (target instanceof Document) {
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
        }

        target?.querySelectorAll('.form-cancel-button')?.forEach(b => {
            b.removeEventListener('click', Cmfive.formCancel);
            b.addEventListener('click', Cmfive.formCancel);
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

        AlertAdaptation.bindCloseEvent();
        DropdownAdaptation.bindDropdownHover();
        FavouritesAdaptation.bindFavouriteInteractions();
        InputWithOther.bindOtherInteractions();
        MultiFileUpload.bindInteractions();
        TabAdaptation.bindTabInteractions();
        TableAdaptation.bindTableInteractions();
        QuillEditor.bindQuillEditor();

        // Remove all foundation button classes and replace them with bootstrap if they don't exist
        target?.querySelectorAll('.button')?.forEach(b =>  {
            b.classList.remove('button', 'tiny')
            if (!b.classList.contains('btn')) {
                b.classList.add('btn', 'btn-sm', 'btn-primary');
            }
        });

        // Bind modal links
        target?.querySelectorAll('[data-modal-target]')?.forEach((m: Element) => {
            m.removeEventListener('click', this.modalClickListener);
            m.addEventListener('click', this.modalClickListener);
        })

        target?.querySelectorAll('[data-link-target]')?.forEach((m: Element) => {
            m.removeEventListener('click', this.linkClickListener);
            m.addEventListener('click', this.linkClickListener);
        })
    }
}

window.addEventListener('load', () => Cmfive.ready(document));
