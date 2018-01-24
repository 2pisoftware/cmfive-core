<?php

echo "Task Group successfully created.<p>";
echo "<div style=\"width:800px;\">";
echo $table;
echo "</div>";

/*
 * js validation does not load unless form is loaded in tpl rather than in creategroup_GET() ???
 * 
	$can_list = array("All" => "All", "Owner" => "Owner", "Member" => "Member", "Guest" => "Guest");
	$is_active = array("0" => "Yes", "1" => "No");
	$is_deleted = array("1" => "Yes", "0" => "No");
	$task_group_type = array("todo" => "To Do", "gclead" => "GC Lead", "shwlead" => "Solar Hot Water");
	
	$f = array(
            array("Task Group Attributes","section"),
            array("Title","text","title"),
            array("Who Can Assign","select","can_assign",null,$can_list),
            array("Who Can View","select","can_view",null,$can_list),
            array("Active","select","is_active",null,$is_active),
            array("Delete","select","is_deleted",null,$is_deleted),
            array("Description","text","description"),
            array("Default Assignee","text","default_assignee_id"),
            array("Task Type","select","task_group_type",null,$task_group_type),
            );

	echo Html::form($f,$w->localUrl("/task/creategroup"),"POST","Save","saveGroup");
*/

?>

<script type="text/javascript">

$("#saveGroup").submit(function(){
        $.fn.colorbox.resize();
        return validateCreateTaskGroup();
    });

    function lvMassValidate(p){
        return LiveValidation.massValidate(p);
    }

    function validateCreateTaskGroup(){
        var vTitle = new LiveValidation('title');
        vTitle.add(Validate.Presence, {failureMessage: " "});
        var canAssign = new LiveValidation('can_assign');
        canAssign.add(Validate.Presence, {failureMessage: " "});
        var canView = new LiveValidation('can_view');
        canView.add(Validate.Presence, {failureMessage: " "});
        var visActive = new LiveValidation('is_active');
        visActive.add(Validate.Presence, {failureMessage: " "});
        var visDeleted = new LiveValidation('is_deleted');
        visDeleted.add(Validate.Presence, {failureMessage: " "})
        var vdescription = new LiveValidation('description');
        vdescription.add(Validate.Presence, {failureMessage: " "});
        var defaultAssigneeId = new LiveValidation('default_assignee_id');
        defaultAssigneeId.add(Validate.Presence, {failureMessage: " "});
        var taskGroupType = new LiveValidation('task_group_type');
        taskGroupType.add(Validate.Presence, {failureMessage: " "});
        return lvMassValidate([vTitle,canAssign,canView,visActive,visDeleted,vdescription,defaultAssigneeId,taskGroupType]);
    }

</script>

