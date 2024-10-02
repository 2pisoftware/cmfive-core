
type Tag = {
	tag: string;

}

type AssignedTags = {
	display: Tag[];
	hover: Tag[];
}

export class Tags {
	private static target = ".tags-container";

	static bind = async () => {
		document.querySelectorAll(Tags.target).forEach(this.update);

		const modal = document.getElementById("cmfive-modal");
		modal.addEventListener("hidden.bs.modal", () => {
			const id = modal.querySelector("input.tom-select-target").id.replace("display_", "");
			console.log(`tags for ${id} updated`);

			this.update(document.getElementById(id).parentElement);
		})
	}

	private static update = async (container: Element) => {
		const id = container.getAttribute("data-tag-id");
		const tags = await fetch(`/tag/ajaxGetTags/${id.replace("_", "/")}`).then(x => x.json());

		const shown = document.getElementById(`tags_${id}`);
		const hidden = document.getElementById(`tags_${id}`);

		shown.innerHTML = "";
		hidden.innerHTML = "";

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
		elem.classList.add("bg-secondary", "tag-small")
		
		return elem;
	}
}