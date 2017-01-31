<?php if ($user) : ?>
<?php if (!empty($box)) : ?><h1>Edit User</h1><?php endif; ?>
<?php
    $contact = $user->getContact();
    $form['User Details'][]=array(
            array("Login","text","login",$user->login),
            array("Admin","checkbox","is_admin",$user->is_admin),
            array("Active","checkbox","is_active",$user->is_active),
            array("External", "checkbox", "is_external", $user->is_external));

    $form['User Details'][]=array(
            array("Password","password","password"),
            array("Repeat Password","password","password2"));

    $form['Contact Details'][]=array(
            array("First Name","text","firstname",$contact ? $contact->firstname : ""),
            array("Last Name","text","lastname",$contact ? $contact->lastname : ""));
    $form['Contact Details'][]=array(
            array("Title","select","title",$contact ? $contact->title : "",lookupForSelect($w, "title")),
            array("Email","text","email",$contact ? $contact->email : ""));
            
	$groupUsers = $user->isInGroups();
    
    if ($groupUsers)
    {
    	foreach ($groupUsers as $groupUser)
    	{
    		$group = $groupUser->getGroup();
    		
    		$groups[] = " - ".Html::a("/admin/moreInfo/".$group->id, $group->login);
    	}
    }
    else
    {
    	$groups = array();
    }
    $form['User Groups'][] = array(array("Group Title","static","groupName",implode("<br/>", $groups)));

    print Html::multiColForm($form,$w->localUrl("/admin/useredit/".$w->ctx("id")),"POST","Save");
?>

<?php else : ?>
<div class="error">User with ID <?php echo $id; ?> does not exist.</div>
<?php endif; ?>

<script type="text/javascript">
	$(".form-section").attr("width","");
</script>