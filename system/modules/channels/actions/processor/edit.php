<?php 

function edit_GET(Web $w) {

	$p = $w->pathMatch("id");
	$processor_id = $p["id"];

	$w->Channels->navigation($w, $processor_id ? "Edit" : "Add" . " a Processor");

	// Get channel and form
	$processor = $processor_id ? $w->Channel->getProcessor($processor_id) : new ChannelProcessor($w);
	$processor_list = $w->Channel->getProcessorList();

	$form = array("Processor" => array(
		array(
			array("Name", "text", "name", $processor->name)
		),
		array(
			array("Channel", "select", "channel_id", $processor->channel_id, $w->Channel->getChannels())
		),
		array(
			array("Processor Class", "select", "processor_class", $processor->module.'.'.$processor->class, $processor_list)
		)
	));

	$w->out(Html::multiColForm($form, "/channels-processor/edit/{$processor_id}", "POST", "Save"));
}

function edit_POST(Web $w) {

	$p = $w->pathMatch("id");
	$processor_id = $p["id"];

	// Break the selected processor up into module and class
	$processor_class = $w->request("processor_class");
	$processor_expl = explode(".", $processor_class);

	// Make sure we only have two values
	if (count($processor_expl) !== 2) {
		$w->error("Missing Processor values", "/channels/listprocessors");
		exit();
	}

	// make sure the selected class exists in config
	if (!in_array($processor_expl[1], $w->moduleConf($processor_expl[0], "processors"))) {
		$w->error("Could not find processor in config", "/channels/listprocessors");	
		exit();
	}

	$processor_object = $processor_id ? $w->Channel->getProcessor($processor_id) : new ChannelProcessor($w);
	$processor_object->fill($_POST);
	$processor_object->channel_id = $w->request("channel_id");
	$processor_object->module = $processor_expl[0];
	$processor_object->class = $processor_expl[1];
	$processor_object->insertOrUpdate();

	$w->msg("Processor " . ($processor_id ? "updated" : "created"), "/channels/listprocessors");

}