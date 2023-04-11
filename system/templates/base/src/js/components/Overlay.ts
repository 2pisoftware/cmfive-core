
export class Overlay {
    private static OVERLAY_TARGET = 'cmfive-overlay';

    static showOverlay() {
        const overlay = document.getElementById(Overlay.OVERLAY_TARGET);
        if (overlay) {
            overlay.style.display = 'flex';
        }
    }

    static hideOverlay() {
        const overlay = document.getElementById(Overlay.OVERLAY_TARGET);
        if (overlay) {
            overlay.style.display = 'none';
        }
    }
}