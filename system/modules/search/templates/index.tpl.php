<div>
	<h3 class="subheading columns large-6">Search</h3>
	<span class="columns large-6" style="text-align: right;">
		<p style="font-size: 12px;">
			<strong>Note:</strong> Search terms must contain minimum 3 characters.
			<br>
			<strong>Tip:</strong> To search by Id, use 'id##' eg. id5.
		</p>
	</span>
</div>
<hr>
<div class="row-fluid">
	<!--    <form action="<?php // echo $webroot; 
							?>/search/results" method="GET">-->
	<form id="search_form" class="clearfix">
		<input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
		<div class="row-fluid">
			<div class="small-12 medium-6 columns">
				<input class="input-large" type="text" name="q" id="q" autofocus />
			</div>
			<div class="small-12 medium-2 columns">
				<?php echo Html::select("idx", $indexes); ?>
			</div>
			<div class="small-12 medium-2 columns">
				<?php echo Html::select("tags", $tags); ?>
			</div>
			<div class="small-12 medium-2 columns">
				<button class="button tiny small-12" type="submit">Go</button>
			</div>
		</div>
	</form>


</div>

<div id="search_message" class="row">
	<div data-alert style="margin-top: 1rem" class="alert-box warning" id="message_box"></div>
</div>

<div id="result" class="row" style="display: none;">

</div>

<script>
	const setError = (str) => {
		if (!str) {
			document.querySelector("#search_message").style.display = "none";
			document.querySelector("#result").style.display = "block";
			return;
		}

		document.querySelector("#message_box").innerText = str;
		document.querySelector("#search_message").style.display = "block";
		document.querySelector("#result").style.display = "none";
	}

	document.querySelector("#search_form").addEventListener("submit", async function(event) {
		event.preventDefault();

		setError(false);

		const form = new FormData(event.target);
		const body = new URLSearchParams(form);

		try {
			const response = await fetch(`/search/results?` + body.toString());

			const json = await response.json();

			if (!json.success)
				return setError(json.data);

			document.querySelector("#result").innerHTML =
				json.data || `<span style="padding-left: 20px;">No results found</span>`;
		} catch (e) {
			setError(`Failed to receive a response from search`);
		}

		return false;
	});
</script>
<br>