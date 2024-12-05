// js/components/Sortable.ts

import { Sortable as SortableLib } from 'sortablejs';

export class Sortable {
    private static sortableTarget = 'data-sortable';

    static bindSortableElements()
    {
        const sortableElements = document.querySelectorAll(`[${Sortable.sortableTarget}]`);

        if (sortableElements) {
            sortableElements.forEach((s: HTMLElement) => {
                new SortableLib(s, {
                    onSort: (e) => {
                        const callback = s.getAttribute('data-on-sort');
                        if (callback) {
                            window[callback](e);
                        }
                    }
                });
            });
        }
    }

}
