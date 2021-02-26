// models/NavStructure.ts

import { NavModule } from "./NavModule";

export class NavStructure {
    public structure: Map<string, NavModule>

    constructor(structure: Map<string, Map<string, string>>) {
        let _structure = new Map();
        structure.forEach((v: Map<string, string>, k: string) => {
            _structure.set(k, new NavModule(v));
        })

        this.structure = _structure;
    }
}