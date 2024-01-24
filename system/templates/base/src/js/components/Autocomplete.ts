
import TomSelect from 'tom-select';

export class Autocomplete {
    private static SELECT_TARGET = 'input[custom_autocomplete]';

    /**
     * @todo check if this works with multiple instances
     */
    static bindInteractions() {
        document.querySelectorAll(Autocomplete.SELECT_TARGET)?.forEach(s => {
            const mut_config = { attributes: true };
            const callback = (mutationList, observer) => {
                for (const mutation of mutationList) {
                    if (mutation.type === "attributes" && mutation.attributeName === "data-url") {
                        console.log('Updating autocomplete data-url')
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
        if (el.tomselect){
            el.tomselect.destroy();
        }
        let config = JSON.parse(el.getAttribute('data-config'));
        let url = el.getAttribute('data-url');
        const callback = function(query, callback) {
			var _url = url + '&term=' + encodeURIComponent(query);
			fetch(_url)
				.then(response => response.json())
				.then(json => {
					callback(json);
				}).catch(()=>{
					callback();
				});
		};

        config['load'] = callback;
        try {
            new TomSelect(el as HTMLInputElement, config ? config : null);
        } catch (e:any) {
            console.error("Failed to setup Tom-Select: ", (e ?? 'Null error'));
        }
    }
}