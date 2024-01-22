
import TomSelect from 'tom-select';

export class Autocomplete {
    private static SELECT_TARGET = 'input[autocomplete]';

    /**
     * @todo check if this works with multiple instances
     */
    static bindInteractions() {
        document.querySelectorAll(Autocomplete.SELECT_TARGET)?.forEach(s => {
            console.log(s)
            const mut_config = { attributes: true };
            const callback = (mutationList, observer) => {
                for (const mutation of mutationList) {
                    if (mutation.type === "attributes" && mutation.attributeName === "data-url") {
                        Autocomplete.setupTomSelect(s);
                    }
                }
            };
            const observer = new MutationObserver(callback);
            observer.observe(s, mut_config);

            Autocomplete.setupTomSelect(s);
        })
    }

    static setupTomSelect(el) {
        let config = JSON.parse(el.getAttribute('data-config'));
        let url = el.getAttribute('data-url');
        const callback = function(query, callback) {
			var _url = url + '?q=' + encodeURIComponent(query);
			fetch(_url)
				.then(response => response.json())
				.then(json => {
					callback(json.items);
				}).catch(()=>{
					callback();
				});
		};

        config['load'] = callback;
        new TomSelect(el as HTMLInputElement, config ? config : null);
    }
}