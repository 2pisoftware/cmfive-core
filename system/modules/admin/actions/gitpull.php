<?php

function gitpull_GET(Web $w) {
    $form = array(
      __("Path to Git") => array(
          array(array(__("Path to Git"), "text", "git"))
      ),
      __("Branch") => array(
          array(array(__("Branch"), "text", "branch"))
      )  
    );
    
    $w->out(Html::multiColForm($form, "/admin/gitpull", "POST", __("Go")));
}

function gitpull_POST(Web $w) {
    $git = $_POST["git"];    
    if (empty($_POST["branch"])) {
        $w->error(__("Branch missing"), "/admin/gitpull");
    }
    if (empty($git)) $git = "git";
    
    chdir(ROOT_PATH);
        
    echo "<pre>";
    echo trim(shell_exec(escapeshellarg($git) . " pull origin " . escapeshellarg($_POST["branch"])));
    echo "</pre>";
}
