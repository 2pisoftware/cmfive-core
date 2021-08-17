// src/js/components/InputWithOther.ts

export class InputWithOther
{
    private static OTHER_ATTRIBUTE = 'data-other-field';
    private static TARGET_VALUE = 'data-other-target-value';

    static bindOtherInteractions()
    {
        // Show other fields on page load
        document.querySelectorAll('[' + InputWithOther.OTHER_ATTRIBUTE + ']')?.forEach(o => {
            const input_id = o.getAttribute(InputWithOther.OTHER_ATTRIBUTE);
            const target_value = o.hasAttribute(InputWithOther.TARGET_VALUE) ? o.getAttribute(InputWithOther.TARGET_VALUE) : 'other';
            if (input_id) {
                const target = document.getElementById(input_id) as HTMLInputElement;
                if ((target.type !== 'checkbox' && target.value === target_value) || target.checked) {
                    o.classList.remove('d-none')
                } else {
                    if (!o.classList.contains('d-none')) {
                        o.classList.add('d-none');
                    }
                }
            }
        });

        // Binds click listeners
        document.querySelectorAll('[' + InputWithOther.OTHER_ATTRIBUTE + ']')?.forEach(o => {
            const input_id = o.getAttribute(InputWithOther.OTHER_ATTRIBUTE);
            if (input_id) {
                const target = document.getElementById(input_id);
                target.removeEventListener('change', () => InputWithOther.changeListener(target as HTMLInputElement, o as HTMLInputElement));
                target.addEventListener('change', () => InputWithOther.changeListener(target as HTMLInputElement, o as HTMLInputElement));
            }
        });
    }

    private static changeListener(target: HTMLInputElement, other_field: HTMLInputElement)
    {
        const target_value = other_field.hasAttribute(InputWithOther.TARGET_VALUE) ? other_field.getAttribute(InputWithOther.TARGET_VALUE) : 'other';
        console.log(target_value);
        if ((target.type !== 'checkbox' && target.value === target_value) || target.checked) {
            other_field.classList.remove('d-none')
        } else {
            if (!other_field.classList.contains('d-none')) {
                other_field.classList.add('d-none');
            }
        }
    }
}
