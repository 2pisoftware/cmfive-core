// models/NavElement.ts

export class NavElement {
    public name: string;
    public path: string;

    public constructor(name: string, path: string) {
        this.name = name;
        this.path = path;
    }

    // public toString(): string {
    //     return "<a class='nav-link' href='" + this.path + '">' + this.name + '</a>';
    // }
}