<?php

function report_core_web_before_get(Web $w)
{
    // build Navigation to Reports for current Module
    if (AuthService::getInstance($w)->loggedIn()) {
        $reports = ReportService::getInstance($w)->getReportsforNav();
        if ($reports) {
            $w->ctx("reports", $reports);
        }
    }
}

// Admin user remove hook
function report_admin_remove_user(Web $w, User $user)
{
    return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "report");
}

/**
 * This method will be called on every action. If the User is an administrator and the
 * report database config is not setup correctly, the error context will be set to
 * display an error message notifying that there is an issue with the config.
 *
 * @param Web $w
 * @return void
 */
function report_core_web_after(Web $w)
{
    $user = AuthService::getInstance($w)->user();
    if (empty($user) || !$user->is_admin) {
        return;
    }

    $config = Config::get("report.database");
    if (empty($config) || empty($config['database']) || empty($config['username']) || empty($config['password'])) {
        $w->ctx("error", "Report database connection not setup in config");
    }
}
