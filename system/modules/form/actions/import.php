<?php

function import_GET(Web $w)
{
	$w->setLayout(null);

	$w->ctx('title',"Form Import");

	$_form = [
		'Select form zip file' => [
			[(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")], // ["File", "file", "file"]
			[[" Form title override (Optional)", "text", "title_override"]]
		]
	];

	$w->ctx("form", HtmlBootstrap5::multiColForm($_form, "/form/import", "POST", "Save"));
}

function import_POST(Web $w) {


	if(isset($_FILES['file'])) {
	    $filename = $_FILES['file']['name'];
	    $source = $_FILES['file']['tmp_name'];
	    $type = $_FILES['file']['type']; 
	     
	    $name = preg_split("/[\:\.]/", $filename, -1, PREG_SPLIT_NO_EMPTY);
	    //check for form dir in uploads
	    if (!is_dir(ROOT_PATH .'/uploads/form')) {
	    	mkdir(ROOT_PATH .'/uploads/form/');
	    }
	    
	     
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
	          $w->error("Please choose a zip file","/form");       
	    }
	      
		//sanitize target filename
		$new_dir = md5($name[0]);
		$target = ROOT_PATH .'/uploads/form/' . $new_dir . '-' . time() . '/';
	    mkdir($target);
		//check if folder was created
		if (realpath($target) != substr($target, 0, -1)) {
			$w->error("Paths don't match", '/form-application');
		}
		if (realpath($target) === false) {
			$w->error('Failed to create folder', '/form-application');
		}

		$new_filename = md5($filename) . '.zip';
	    $saved_file_location = realpath($target) . $new_filename;
	     
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
	    	$w->error('no content found. PLease ensure that your zip filename matches your report name');
	    }

	    //delete file upload from directory
	    unlink($target . $new_filename);
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
	    $w->msg('Form import completed','/form');
	} else {
		$w->error('No upload found','/form');
	}
}
