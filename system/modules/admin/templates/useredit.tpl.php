<?php if ($user) : ?>
<?php if (!empty($box)) : ?><h1><?php _e('Edit User'); ?></h1><?php endif; ?>
<?php
    $contact = $user->getContact();
    $form['User Details'][]=array(
            array(__("Login"),"text","login",$user->login),
            array(__("Admin"),"checkbox","is_admin",$user->is_admin),
            array(__("Active"),"checkbox","is_active",$user->is_active),
            array(__("Language"),"select","language",$user->language,$availableLocales));

    $form['User Details'][]=array(
            array(__("Password"),"password","password"),
            array(__("Repeat Password"),"password","password2"));

    $form['Contact Details'][]=array(
            array(__("First Name"),"text","firstname",$contact ? $contact->firstname : ""),
            array(__("Last Name"),"text","lastname",$contact ? $contact->lastname : ""));
    $form['Contact Details'][]=array(
            array(__("Title"),"select","title",$contact ? $contact->title : "",lookupForSelect($w, "title")),
            array(__("Email"),"text","email",$contact ? $contact->email : ""));
            
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
    $form[__('User Groups')][] = array(array(__("Group Title"),"static","groupName",implode("<br/>", $groups)));

    print Html::multiColForm($form,$w->localUrl("/admin/useredit/".$w->ctx("id")),"POST",__("Save"));
?>

<?php else : ?>
<div class="error"><?php _e('User with ID'); ?> <?php echo $id; ?> <?php _e('does not exist.'); ?></div>
<?php endif; ?>

<script type="text/javascript">
	$(".form-section").attr("width","");
</script>
