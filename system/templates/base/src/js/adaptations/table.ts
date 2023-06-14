
export class TableAdaptation {
    static tables: NodeListOf<HTMLElement> = document.querySelectorAll("table.tablesorter");
    static sortedClass = 'sorted-asc';

    static headerClickEvent = (header: HTMLTableHeaderCellElement, table: HTMLElement, index: number) => {
        // const td = table.querySelectorAll('tr td:nth-child('+ (index + 1) + ')')
        let ascDirection = true;
        if (header.classList.contains(TableAdaptation.sortedClass)) {
            ascDirection = false;
            header.classList.remove(TableAdaptation.sortedClass);
        } else {
            table.querySelectorAll('thead th')?.forEach(th => th.classList.remove(TableAdaptation.sortedClass))
            header.classList.add(TableAdaptation.sortedClass);
        }

        const tbody = table.querySelector('tbody');
        const tr = Array.from(tbody.querySelectorAll('tr'));
        tr?.sort((a: HTMLTableRowElement, b: HTMLTableRowElement) => {
            if (ascDirection === false) {
                const c = b;
                b = a;
                a = c;
            }
            return (a.querySelector('td:nth-child(' + (index + 1) + ')') as HTMLTableColElement)?.innerText.localeCompare((b.querySelector('td:nth-child(' + (index + 1) + ')') as HTMLTableColElement)?.innerText)
        })

        tbody.querySelectorAll('tr')?.forEach(row => tbody?.removeChild(row));
        tr?.forEach(row => tbody.appendChild(row));
    }

    static bindTableInteractions = () => {
        TableAdaptation.tables?.forEach(table => {
            table.querySelectorAll('th')?.forEach((thead, index) => {
                thead.addEventListener('click', () => {
                    TableAdaptation.headerClickEvent(thead, table, index)
                })
            })
        });
    }
}
