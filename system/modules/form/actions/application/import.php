<?php

function import_GET(Web $w) {

	$w->ctx('title',"Form Import");

	$_form = [
		'Select application zip file' => [
			[(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")], // ["File", "file", "file"]
			[[" Application title override (Optional)", "text", "title_override"]]
		]
	];

	$w->ctx("form", Html::multiColForm($_form, "/form-application/import", "POST", "Save"));
}

function import_POST(Web $w) {


	if(isset($_FILES['file'])) {
	    $filename = $_FILES['file']['name'];
	    $source = $_FILES['file']['tmp_name'];
	    $type = $_FILES['file']['type']; 
	     
	    $name = explode('.', $filename); 
	    //check for form dir in uploads
	    if (!is_dir(ROOT_PATH .'/uploads/form')) {
	    	mkdir(ROOT_PATH .'/uploads/form/');
	    }
	    $target = ROOT_PATH .'/uploads/form/' . $name[0] . '-' . time() . '/';  
	     
	    // Ensures that the correct file was chosen
	    $accepted_types = array('application/zip', 
	                                'application/x-zip-compressed', 
	                                'multipart/x-zip', 
	                                'application/s-compressed');
	 
	    foreach($accepted_types as $mime_type) {
	        if($mime_type == $type) {
	            $okay = true;
	            break;
	        } 
	    }
	       
	  //Safari and Chrome don't register zip mime types. Something better could be used here.
	    $okay = strtolower($name[1]) == 'zip' ? true: false;
	 
	    if(!$okay) {
	          $w->error("Please choose a zip file","/form-application");       
	    }
	    
	    mkdir($target);
	    $saved_file_location = $target . $filename;
	     
	    if(move_uploaded_file($source, $saved_file_location)) {
	        $zip = new ZipArchive();
		    $x = $zip->open($saved_file_location);
		    if($x === true) {
		        $zip->extractTo($target);
		        $zip->close();
		         
		        unlink($saved_file_location);
		    } else {
		    	$w->error('failed opening zip',"/form");
		    }
	    } else {
	        $w->error("Failed to save file upload","/form");
	    }
	     
	    $content = json_decode(file_get_contents($target.$name[0]));
	    if (empty($content)) {
	    	$w->error('no content found. PLease ensure that your zip filename matches your application name');
	    }

	    //delete file upload from directory
	    unlink($target.$name[0]);
	    rmdir($target);
	//echo $target.$name[0]; die;
	    //create form structure from $content
	    if (!empty($content)) {
	    	//check for form title override and check title
	    	if (isset($_POST['title_override']) && !empty($_POST['title_override'])) {
	    		$new_title = $_POST['title_override'];
	    	} else {
	    		$new_title = $content->title;
	    	}
	    	$new_title = $w->Form->checkImportedApplicationTitle($new_title);

	    	$new_application = new FormApplication($w);
	    	$new_application->title = $new_title;
	    	$new_application->description = $content->description;
	    	$new_application->is_active = 1;
	    	$new_application->insert();

	    	if (!empty($content->forms)) {
	    		foreach ($content->forms as $form) {
	    			$new_form_title = $form->form_title;

	    			$new_form = $w->Form->importForm($new_form_title,$form);

	    			if (!empty($new_form)) {
	    				$form_app_map = new FormApplicationMapping($w);
	    				$form_app_map->form_id = $new_form->id;
	    				$form_app_map->application_id = $new_application->id;
	    				$form_app_map->insert();
	    			}

	    		}
	    	}

	    	
	    }
	    $w->msg('Application import completed','/form-application');
	} else {
		$w->error('No upload found','/form-application');
	}
}
