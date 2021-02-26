// models/NavModule.ts

import { NavElement } from "./NavElement";

export class NavModule {
    public topMenu: boolean;
    public navs: Array<NavElement> = [];

    public constructor(navs: Map<string, string>) {
        // this.topMenu = topMenu;

        let _navs = [];
        navs.forEach((k: string, v: string) => {
            _navs.push(new NavElement(k, v))
        });

        this.navs = _navs;
    }
}