
import TomSelect from 'tom-select';

// NOTE: Prev name: MultiSelect
export class Autocomplete {
    private static SELECT_TARGET = '.tom-select-target';

    static bindInteractions() {
        document.querySelectorAll(Autocomplete.SELECT_TARGET)?.forEach(s => {
            const config = s.getAttribute('data-config');
            const parsed = config ? JSON.parse(config) : {};

            if (parsed.source) {
                parsed.load = Autocomplete.tomSelectLoad.bind(null, parsed.source);
                parsed.shouldLoad = (query: string) => query.length > 1;
            }

            new TomSelect(s as HTMLInputElement, parsed);
        })
    }

    static tomSelectLoad = (
        source: string,
        query: string,
        callback: (items?: Array<{ value: string, text: string }>) => unknown
    ) => {
        const url = new URL(source);
        url.searchParams.append("term", query);
        
        fetch(url)
            .then(res => res.json())
            .then(json => {
                if (!Array.isArray(json)) throw new Error("Invalid response data");

                const data = json.map(x => {
                    if ('label' in x) x.text = x.label; // compat
                    return x;
                });

                callback(data);
            })
            .catch(() => callback());
    }
}