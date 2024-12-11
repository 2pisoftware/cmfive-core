// src/js/components/TabbedPagination.ts

export class TabbedPagination {
    static bindInteractions() {
        document.querySelectorAll('a[data-tabbed-pagination-page]').forEach(a => {
            a.removeEventListener('click', TabbedPagination.clickHandler);
            a.addEventListener('click', TabbedPagination.clickHandler);
        });
    }

    static clickHandler = function() {
        const tab = this.getAttribute('data-tab');
        const page = +this.getAttribute('data-tabbed-pagination-page');

        // element containing all paginated content (includes pagination controls, so watch out)
        const pages = this.closest(`[id="${tab}-tabbed-pagination"]`);
        const controls = this.closest(`[id="${tab}-pagination-controls"]`)

        // search for child of pages that has `data-page-number` attribute and DOESN'T have a class of `d-none`
        // this will be the currently visible page
        // add `d-none` class to that element
        pages
            .querySelector('[data-page-number]:not(.d-none)')
            .classList.add('d-none');

        // search for child of controls that has classes "active" and "disabled"
        // this is the currently active pages's button
        // remove "active" and "disabled" classes from that element
        controls
            .querySelector('.active.disabled')
            .classList.remove('active', 'disabled');

        // search for child of pages that has `data-page-number` attribute equal to the clicked page
        // remove `d-none` class from that element
        pages
            .querySelector(`[data-page-number="${page}"]`)
            .classList.remove('d-none');

        // add "active" and "disabled" classes to the clicked button
        this.closest('.page-item').classList.add('active', 'disabled');
    }
}