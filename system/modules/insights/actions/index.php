<?php
/**@author Alice Hutley <alice@2pisoftware.com> */

function index_ALL(Web $w)
{
  $w->ctx("title", "Insights List");

  //get userId for logged in user
  $user_id =   AuthService::getInstance($w)->user()->id;
  //var_dump($user_id);
  //die;

  // access service functions using the Web $w object and the module name
  $modules = InsightService::getInstance($w)->getAllInsights('all');
  //var_dump($modules);

  //Display a list of all the insights this user can see
  // build the table array adding the headers and the row data
  $table = [];
  $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
  if (!empty($modules)) {
    foreach ($modules as $modulename => $insights) {
      if (!empty($insights)) {
        foreach ($insights as $insight) {
          //var_dump($insight);
          //die;
          if (InsightService::getInstance($w)->IsMember(Get_class($insight), $user_id)) {
            $row = [];
            // add values to the row in the same order as the table headers
            $row[] = $insight->name;
            $row[] = $modulename;
            $row[] = $insight->description;
            // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
            $actions = [];
            $actions[] = Html::b('/insights/viewInsight/' . Get_class($insight), 'View');
            if (
              InsightService::getInstance($w)->isInsightOwner($user_id, get_class($insight))
            ) {
              $actions[] = Html::b('/insights/manageMembers?insight_class=' . Get_class($insight), 'Manage Members');
            }
            $row[] = implode('', $actions);
            $table[] = $row;
          }
        }
      }
    }
  }

  //send the table to the template using ctx
  $w->ctx('insightTable', Html::table($table, 'insight_table', 'tablesorter', $tableHeaders));
}
