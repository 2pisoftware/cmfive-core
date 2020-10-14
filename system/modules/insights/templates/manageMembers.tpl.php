<?php 
$insight_class = $w->request('insight_class');
echo Html::box("/insights-members/editMembers", "Add new member", true);
echo $membersTable;
