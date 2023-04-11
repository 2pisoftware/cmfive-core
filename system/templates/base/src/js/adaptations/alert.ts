// js/adaptations/alert

export class AlertAdaptation {
    // bind close event
    private static closeTarget = '.alert .close';

    static bindCloseEvent()
    {
        document.querySelectorAll(AlertAdaptation.closeTarget)?.forEach(a => {
            a.removeEventListener('click', () => a.closest('.alert').remove())
            a.addEventListener('click', () => a.closest('.alert').remove())
        });
    }
}