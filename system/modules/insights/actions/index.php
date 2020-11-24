<?php
//$tbl_name = "insight_members"//Table name for data needed to check below.
//session_start();
//if (match_found_in_database()) {
  //$_SESSION['loggedin'] = true;
  //$_SESSION['user_id'] = $user_id; // $username coming from the form, such as $_POST['username']
                                      // something like this is optional, of course
  //foreach ($user_id) {
    //add code for checking insight_class_name and type in daatabse for each entry of user_id of logged in user.
  //}
//}
//if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
  //foreach ($insight_class_name) {
    //code for checking database for entries of logged in users_id against each insight_class_name
    //if insight_class-name matches user_id for logged in user, echo $name and $description and $module of insight class for that insight class. Then check type in same row against user_id and insight_class_name.
    //if type = MEMBER display View button. If type = OWNER display View and Manage Memebers buttons.
  //}
//}
//need something like code below. Finds each instance of logged in users id in Members Table and checks for an insight_class_name and displays corresponing information about that class. Then checks type in members table for that row and displays buttons accordingly.
//getUser
//$user = $SESSION['user_id'];
//foreach ($user_id) {
  //if (insight_class_name = (! empty)){
    //echo $insight->name, $name, $insight->xmlrpc_parse_method_description
    //else skip
  //}
  //if (insight_class_name = (! empty)){
    //if (type = MEMBER) {
      //echo $actions view
    //}
    //else {
      //echo $actions view, $actions Manage Members
    //}
//}

function index_ALL(Web $w)
{
    $w->ctx("title", "Insights List");



    LogService::getInstance($w)->setLogger("INSIGHTS")->error("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->info("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->debug("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->warn("This is an INSIGHTS.INFO message");

    //get userId for logged in user
    $user_id =   AuthService::getInstance($w)->user()->id;

    // access service functions using the Web $w object and the module name
    $modules = InsightService::getInstance($w)->getAllInsights('all');
    //var_dump($modules);

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
    if (!empty($modules)) {
        foreach ($modules as $name=>$module) {
            if (!empty($module)) {
              foreach ($module as $insight){
                if (IsMember = true) {
                  $row = [];
                  // add values to the row in the same order as the table headers
                  $row[] = $insight->name;
                  $row[] = $name;
                  $row[] = $insight->description;
                  // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
                  $actions = [];
                  $actions[] = Html::b('/insights/viewInsight/' . Get_class($insight),'View');
                  $actions[] = Html::b('/insights/manageMembers?insight_class=' . Get_class($insight),'Manage Members');
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
