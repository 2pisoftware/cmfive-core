<?php

function viewInsight_GET(Web $w) {
    // now we need to fetch the correct insight
    // we will use pathMatch to retrieve an insight name from the url.
    [$insight_class] = $w->pathMatch("insight_class");    // $insight_class will contain whatever you put after the slash following the action name
    // if the insight name exists we will retrieve the data for that insight
    //var_dump (class_exists($insight_class));
    //var_dump (class_implements($insight_class));
    //die();
    if (empty($insight_class)||!class_exists($insight_class) || !is_subclass_of($insight_class, "InsightBaseClass")) {
      $w->error('Insight does not exist', '/insights');
    }

        // ============= Members tab ===================
        $members = InsightService::getInstance($w)->getInsightMembers($insight_class);

        // set columns headings for display of members
        $line[] = ["Member", "Insight Class Name", "Type", ""];

        // if there are members, display their full name, role and button to delete the member
        if ($members) {
            foreach ($members as $member) {
                $line[] = [
                    $w->Insight->getUserById($member->user_id),
                    $member->insight_class_name->$insight_class->name,
                    $member->type,
                    Html::box("/insight/editmember/" . $insight_class . "/" . $member->user_id, " Edit ", true) .
                    Html::box("/insight/deletemember/" . $insight_class . "/" . $member->user_id, " Delete ", true),
                ];
            }
        } else {
            // if there are no members, say as much
            $line[] = ["Group currently has no members. Please Add New Members.", "", ""];
        }

        // display list of group members
        $w->ctx("viewinsightmembers", Html::table($line, null, "tablesorter", true));

        // =========== template tab ======================
        $insight_templates = $insight_class->getTemplates();

        // Build table
        $table_header = ["Title", "Category", "Is Email Template", "Type", "Actions"];
        $table_data = [];

        if (!empty($insight_templates)) {
            // Add data to table layout
            foreach ($insight_templates as $insight_template) {
                $template = $insight_template->getTemplate();
                $table_data[] = [
                    $template->title,
                    $template->category,
                    $insight_template->is_email_template ? "Yes" : "No",
                    $insight_template->type,
                    Html::box("/insight-templates/edit/{$insight_class}/{$insight_template->id}", "Edit", true) .
                    Html::b("/insight-templates/delete/{$insight_template->id}", "Delete", "Are you sure you want to delete this Insight template entry?"),
                ];
            }
        }
        // Render table
        $w->ctx("insight_templates_table", Html::table($table_data, null, "tablesorter", $table_header));

    //add a title to the action
    // change the title to reflect viewing insight
    $w->ctx('title', 'View Insight');
}
?>    