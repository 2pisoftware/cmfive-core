<?php

function gitpull_GET(Web $w) {
    $form = array(
      "Path to Git" => array(
          array(array("Path to Git", "text", "git"))
      ),
      "Branch" => array(
          array(array("Branch", "text", "branch"))
      )  
    );
    
    $w->out(Html::multiColForm($form, "/admin/gitpull", "POST", "Go"));
}

function gitpull_POST(Web $w) {
    $git = $_POST["git"];    
    if (empty($_POST["branch"])) {
        $w->error("Branch missing", "/admin/gitpull");
    }
    if (empty($git)) $git = "git";
    
    chdir(ROOT_PATH);
        
    echo "<pre>";
    echo trim(shell_exec(escapeshellarg($git) . " pull origin " . escapeshellarg($_POST["branch"])));
    echo "</pre>";
}