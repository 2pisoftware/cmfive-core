
export class DropdownAdaptation {
    static dropdown: HTMLElement = document.querySelector(".dropdown");
    // dropdownToggle = document.querySelector(".dropdown-toggle");
    // dropdownMenu = document.querySelector(".dropdown-menu");
    static dropdownToggleClass = ".dropdown-toggle";
    static dropdownMenuClass = ".dropdown-menu";
    static showClass = "show";

    static mouseEnterEvent = () => {
        DropdownAdaptation.dropdown.classList.add(DropdownAdaptation.showClass);
        DropdownAdaptation.dropdown.querySelectorAll(DropdownAdaptation.dropdownToggleClass).forEach((elem: Element) => elem.setAttribute("aria-expanded", "true"));
        DropdownAdaptation.dropdown.querySelectorAll(DropdownAdaptation.dropdownMenuClass).forEach((elem: Element) => elem.classList.add(DropdownAdaptation.showClass));
    }

    static mouseLeaveEvent = () => {
        DropdownAdaptation.dropdown.classList.remove(DropdownAdaptation.showClass);
        DropdownAdaptation.dropdown.querySelectorAll(DropdownAdaptation.dropdownToggleClass).forEach((elem: Element) => elem.setAttribute("aria-expanded", "false"));
        DropdownAdaptation.dropdown.querySelectorAll(DropdownAdaptation.dropdownMenuClass).forEach((elem: Element) => elem.classList.remove(DropdownAdaptation.showClass));
    }

    static bindDropdownHover = () => {
        document.removeEventListener('load resize', DropdownAdaptation.resizeEvent)
        document.addEventListener("load resize", DropdownAdaptation.resizeEvent)
    }

    private static resizeEvent = function() {
        if (this.matchMedia("(min-width: 768px)").matches) {
            DropdownAdaptation.dropdown.addEventListener('mouseenter', DropdownAdaptation.mouseEnterEvent);
            DropdownAdaptation.dropdown.addEventListener('mouseleave', DropdownAdaptation.mouseLeaveEvent);
        } else {
            DropdownAdaptation.dropdown.removeEventListener('mouseenter', DropdownAdaptation.mouseEnterEvent)
            DropdownAdaptation.dropdown.removeEventListener('mouseleave', DropdownAdaptation.mouseLeaveEvent)
        }
    }
}
