
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

			// tbh I would prefer a save button, but the cmfive api is already set up for add, remove, create methods
			// (wrt the tags module)

			if (parsed?.cmfive?.onItemAdd) {
				parsed.onItemAdd = function(item: string) {
					this.setTextboxValue("");
					this.refreshOptions();
					Autocomplete.tomSelectChange.call(null, new URL(parsed.cmfive.onItemAdd), item)
				};
			}
			else {
				parsed.onItemAdd = function() {
					this.setTextboxValue("");
					this.refreshOptions();
				}
			}

			if (parsed?.cmfive?.onItemRemove)
				parsed.onItemRemove = Autocomplete.tomSelectChange.bind(null, new URL(parsed.cmfive.onItemRemove));
			if (parsed?.cmfive?.onItemCreate)
				parsed.onOptionAdd = Autocomplete.tomSelectChange.bind(null, new URL(parsed.cmfive.onItemCreate));

			try {
           		new TomSelect(s as HTMLInputElement, parsed);
			}
			catch (e) {
				console.warn("tomselect errored", e);
			}
        })
    }

	static tomSelectChange = (
		endpoint: URL,
		value: string
	) => {
		endpoint.searchParams.set("value", value);

		fetch(endpoint, {
			method: "POST",
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