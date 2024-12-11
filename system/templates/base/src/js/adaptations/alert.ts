// js/adaptations/alert
import { BaseComponent } from '../BaseComponent';

export class AlertAdaptation implements BaseComponent {
    // bind close event
    private static closeTarget = '.alert .close';

    static bind(target: Document | Element): void
    {
        target.querySelectorAll(AlertAdaptation.closeTarget)?.forEach(a => {
            a.removeEventListener('click', () => a.closest('.alert').remove())
            a.addEventListener('click', () => a.closest('.alert').remove())
        });
    }
}