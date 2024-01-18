
import TomSelect from 'tom-select';

export class MultiSelect {
    private static SELECT_TARGET = '.tom-select-multiselect';

    static bindInteractions() {
        document.querySelectorAll(MultiSelect.SELECT_TARGET)?.forEach(s => {
            const config = s.getAttribute('data-config');
            new TomSelect(s as HTMLInputElement, config ? JSON.parse(config) : null);
        })
    }
}