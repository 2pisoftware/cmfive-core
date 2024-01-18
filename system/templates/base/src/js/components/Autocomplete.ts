
import TomSelect from 'tom-select';

export class Autocomplete {
    private static SELECT_TARGET = '.tom-select-autocomplete';

    static bindInteractions() {
        document.querySelectorAll(Autocomplete.SELECT_TARGET)?.forEach(s => {
            const config = s.getAttribute('data-config');
            new TomSelect(s as HTMLInputElement, {
                valueField: 'url',
                labelField: 'name',
                searchField: ['name'],
                create: false,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    fetch(`${s.getAttribute('autocomplete-url')}/${query}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            callback(data);
                        });
                },
            });
            console.log(s);
        })

        document.querySelectorAll(Autocomplete.SELECT_TARGET)?.forEach(s => {
            s.removeEventListener('change', Autocomplete.changeHandler);
            s.addEventListener('change', Autocomplete.changeHandler);
        })
    }

    static changeHandler = function() {
        console.log("Autocomplete change handler triggered");
    }
}