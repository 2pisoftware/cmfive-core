<?php

if (!empty($user)) {
    if (!empty($box)) {
        echo "<h1>Edit User</h1>";
    }
}

if (!empty($form)) {
    echo $form;
} else {
    echo "<div class=\"error\">User does not exist.</div>";
}

?>

<script type="text/javascript">
    $(".form-section").attr("width","");
</script>