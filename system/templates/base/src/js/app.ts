// src/app.ts
import { AlertAdaptation, DropdownAdaptation, FavouritesAdaptation, TabAdaptation, TableAdaptation } from './adaptations';
import { Autocomplete, Toast as CmfiveToast, CodeMirror, InputWithOther, MultiFileUpload, Overlay, QuillEditor, Tags, TabbedPagination } from './components';

import { Modal, Tooltip } from 'bootstrap';
import { Sortable } from './components/Sortable';

import '../scss/app.scss';

type window = Window & typeof globalThis & { cmfiveEventBus: Comment, cmfive: { toast: typeof CmfiveToast } };

export class Cmfive {
    static THEME_KEY = 'theme';
    static currentModal: Modal;

    static toggleTheme() {
        // debugger;
        const theme = localStorage.getItem(Cmfive.THEME_KEY);
        if (!theme) {
            localStorage.setItem(Cmfive.THEME_KEY, 'default');
        } else {
            if (theme === 'default') {
                localStorage.setItem(Cmfive.THEME_KEY, 'dark');
            } else {
                localStorage.setItem(Cmfive.THEME_KEY, 'default');
            }
        }

		document.querySelector("html").setAttribute("data-bs-theme", localStorage.getItem(Cmfive.THEME_KEY) === "dark" ? "dark" : "light");

        document.querySelector('html').classList.remove('theme--default');
        document.querySelector('html').classList.remove('theme--dark');
        document.querySelector('html').classList.add('theme--' + localStorage.getItem(Cmfive.THEME_KEY));
        fetch('/auth/ajax_set_setting?key=bs5-theme&value=' + localStorage.getItem(Cmfive.THEME_KEY)); // .then(r => r.text()).then(r => console.log(r));
    }

    static toggleNavSettings() {
        const navBackup = document.getElementById('accordion_menu').innerHTML;

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

        if (this.hasAttribute('data-show-overlay')) {
            Overlay.showOverlay();
        }
        
        if (this.hasAttribute('data-link-new-tab')) {
            window.open(this.getAttribute('data-link-target'));
        } else {
            window.location.href = this.getAttribute('data-link-target');
        }
    }

    static openModal(url: string, target: string = '#cmfive-modal') {
        Cmfive.currentModal = new Modal(document.getElementById('cmfive-modal')) //, options
    
        let modalContent = document.querySelector(target + ' .modal-content');
        if (modalContent) {
            modalContent.innerHTML = '<button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="' + target + '" aria-label="Close"></button>';
        }
    
        Cmfive.currentModal.show();
        fetch(url, {
            headers: {
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.text()
        }).then((content) => {
            modalContent.innerHTML = content + modalContent.innerHTML;

			// https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML#security_considerations
			// Appending scripts to the DOM via innerHTML is not meant to execute them for security purposes
			// Unfortunately, various modals however contian script tags we need to execute
			modalContent.querySelectorAll("script").forEach(x => {
				eval(x.innerHTML);
			});

            // Emit modal load event
            // @ts-ignore
            if (window.cmfiveEventBus) {
                // @ts-ignore
                window.cmfiveEventBus.dispatchEvent(new CustomEvent('modal-load'));
            }
    
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
    static ready(target: Document|Element) 
    {
        (window as window).cmfive = {
            toast: CmfiveToast
        }

        // Add offset for breadcrumb if scrollbar is visible
        const breadcrumb = document.querySelector('#breadcrumbs .breadcrumb');
        if (breadcrumb) {
            if (breadcrumb.scrollWidth > breadcrumb.clientWidth) {
                breadcrumb.classList.add('scroll-active');
            }
        }

        // Set up bootstrap tooltips
        target?.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltip) => {
            return new Tooltip(tooltip)
        })

        target?.querySelectorAll('.form-cancel-button')?.forEach(b => {
            b.removeEventListener('click', Cmfive.formCancel);
            b.addEventListener('click', Cmfive.formCancel);
        })

        // Theme toggle
        target?.querySelectorAll('[data-toggle-theme]')?.forEach(t => {
            t.removeEventListener('click', Cmfive.toggleTheme);
            t.addEventListener('click', Cmfive.toggleTheme);
        })

        // Nav settings toggle
        target?.querySelectorAll('[data-toggle-nav-settings]')?.forEach(t => {
            t.removeEventListener('click', Cmfive.toggleNavSettings);
            t.addEventListener('click', Cmfive.toggleNavSettings);
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

        target?.querySelectorAll('form[data-show-overlay]')?.forEach(f => {
            f.removeEventListener('submit', () => Overlay.showOverlay());
            f.addEventListener('submit', () => Overlay.showOverlay());
        })

        // target?.querySelectorAll('[data-show-overlay]')?.forEach(o => {
        //     o.removeEventListener('click', Overlay.showOverlay);
        //     o.addEventListener('click', Overlay.showOverlay);
        // })

        AlertAdaptation.bind(target);
        CodeMirror.bindCodeMirrorEditor();
        DropdownAdaptation.bindDropdownHover();
        FavouritesAdaptation.bindFavouriteInteractions();
        InputWithOther.bindOtherInteractions();
        MultiFileUpload.bindInteractions();
        Autocomplete.bindInteractions();
        QuillEditor.bindQuillEditor();
        Sortable.bindSortableElements();
        TabAdaptation.bindTabInteractions();
        TableAdaptation.bindTableInteractions();
		Tags.bind();
        TabbedPagination.bindInteractions();

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

if (!window.hasOwnProperty('cmfiveEventBus')) {
    // @ts-ignore
    window.cmfiveEventBus = document.createComment('Helper')
    // @ts-ignore
    window.cmfiveEventBus.addEventListener('dom-update', (event) => {
        console.log("DOM EVENT", event); // => detail-data
        Cmfive.ready(event.detail);
    });
}

window.addEventListener('load', () => Cmfive.ready(document));
