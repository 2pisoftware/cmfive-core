<?php

use Html\Form\InputField\File;
use Html\Form\InputField;

function import_GET(Web $w) {
	$w->setLayout('layout-bootstrap-5');
	$w->ctx('title', "Form Import");

	$w->out(
		HtmlBootstrap5::multiColForm(
			[
				'Select form zip file' => [
					[
						new File(
							[
								'id|name' => 'file'
							]
						)
					],
					[
						new InputField(
							[
								'id|name' => 'title_override', 
								'label' => 'Form title override (Optional)'
							]
						)
					]
				]
			]
		),
		"/form/import"
	);
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
	    $accepted_types = [
			'application/zip', 
	        'application/x-zip-compressed', 
	        'multipart/x-zip', 
	        'application/s-compressed'
		];
	 
	    foreach($accepted_types as $mime_type) {
	        if($mime_type == $type) {
	            $okay = true;
	            break;
	        } 
	    }
	       
	  //Safari and Chrome don't register zip mime types. Something better could be used here.
	    $okay = strtolower($name[1]) == 'zip' ? true: false;
	 
	    if(!$okay) {
	          $w->error("Please choose a zip file", "/form");       
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
		    	$w->error('failed opening zip', "/form");
		    }
	    } else {
	        $w->error("Failed to save file upload", "/form");
	    }
	     
	    $content = json_decode(file_get_contents($target.$name[0]));
	    if (empty($content)) {
	    	$w->error('no content found. PLease ensure that your zip filename matches your report name', '/form');
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
	    		$new_title = $content->form_title;
	    	}
	    
	    	FormService::getInstance($w)->importForm($new_title,$content);

	    }
	    $w->msg('Form import completed', '/form');
	} else {
		$w->error('No upload found', '/form');
	}
}
