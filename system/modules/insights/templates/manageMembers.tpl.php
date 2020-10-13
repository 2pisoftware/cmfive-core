<?php 
$insight_class = $w->request('insight_class');
echo Html::b("/insights-members/editMembers" . $insight_class, "Add new member", true);
echo $membersTable;
