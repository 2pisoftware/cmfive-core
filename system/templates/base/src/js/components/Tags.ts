
type Tag = {
	tag: string;

}

type AssignedTags = {
	display: Tag[];
	hover: Tag[];
}

export class Tags {
	private static target = ".tags-container";

	private static selected: Set<string> = new Set();

	static bind = async () => {
		const modal = document.getElementById("cmfive-modal");
		// on modal hide, update
		const cb = () => {
			const id = modal.querySelector("input.tom-select-target")?.id?.replace("display_", "");
			if (!id) return;
			console.log(`tags for ${id} updated`);

			this.update(document.getElementById(id).parentElement);

			modal.removeEventListener("hidden.bs.modal", cb);
		};
		modal?.addEventListener("hidden.bs.modal", cb);
	}

	private static update = async (container: Element) => {
		const loader = container.getElementsByClassName("loader")[0];
		
		const id = container.getAttribute("data-tag-id");
		console.log(`updating ${id}`);
		const shown = document.getElementById(`tags_${id}`);
		const hidden = document.getElementById(`hidden_tags_${id}`);

		shown.innerHTML = "";
		hidden.innerHTML = "";

		loader.classList.remove("d-none");

		const tags = await fetch(`/tag/ajaxGetTags/${id.replace("_", "/")}`).then(x => x.json());

		loader.classList.add("d-none");

		shown.innerHTML = "";
		hidden.innerHTML = "";

		if (!tags.hover.length) container.getElementsByClassName("show_more")[0].classList.add("d-none");
		else container.getElementsByClassName("show_more")[0].classList.remove("d-none");

		if (tags.display.length === 0) {
			return shown.appendChild(this.createTagElement("No tags"));
		}

		tags.display.forEach(tag => shown.appendChild(this.createTagElement(tag)));
		tags.hover.forEach(tag => hidden.appendChild(this.createTagElement(tag)));
	
	}

	private static createTagElement = (tag: Tag | string) => {
		const text = typeof tag === "string" ? tag : tag.tag;

		const elem = document.createElement("span");
		elem.innerText = text;
		elem.classList.add("bg-secondary", "tag-small", "text-light")
		
		return elem;
	}
}