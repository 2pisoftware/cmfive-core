const form = document.getElementById("user_details_form") as HTMLFormElement;

form.addEventListener("submit", async (e) => {
	e.preventDefault();

	// show the loading indicator
	document.getElementById("cmfive-overlay")!.style.display = "flex";

	const data = new FormData(form);
	const obj = {} as any; // don't want to type it, sorry
	data.forEach((value, key) => obj[key] = value);

	const user_id = obj.user_id;
	delete obj.user_id;

	const res = await fetch("/auth/ajax_update_account_details", {
		method: "POST",
		body: JSON.stringify({
			id: user_id,
			account_details: obj,
		})
	});

	// hide the loading indicator
	document.getElementById("cmfive-overlay")!.style.display = "none";

	if (!res.ok) {
		displayToast("Failed to update user details");
		return;
	}

	displayToast("Account details updated");
})

const displayToast = (msg: string) => {
	//@ts-ignore
	(new window.cmfive.toast(msg)).show();
}