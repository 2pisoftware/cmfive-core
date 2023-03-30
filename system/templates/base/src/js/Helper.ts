// src/js/Helper.ts

export class Helper {
    /**
     * Returns the previous sibling of an element until the target class is found
     * or null if not found
     * 
     * @param source current element
     * @param target_selector class we're looking for
     * @returns Element|null
     */
     public static getPreviousSibling(source: Element, target_selector: string): Element {
        var sibling = source?.previousElementSibling;

        // If the sibling matches our selector, use it
        // If not, jump to the next sibling and continue the loop
        while (sibling) {
            if (sibling.matches(target_selector)) {
                break;
            }
            sibling = sibling.previousElementSibling
        }

        return sibling;
    }

    /**
     * Returns the next sibling of an element until the target class is found
     * or null if not found
     * 
     * @param source current element
     * @param target_selector class we're looking for
     * @returns Element|null
     */
    public static getNextSibling(source: Element, target_selector: string): Element {
        var sibling = source?.nextElementSibling;

        // If the sibling matches our selector, use it
        // If not, jump to the next sibling and continue the loop
        while (sibling) {
            if (sibling.matches(target_selector)) {
                break;
            }
            sibling = sibling.nextElementSibling
        }

        return sibling;
    }
}