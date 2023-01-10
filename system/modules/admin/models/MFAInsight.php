<?php

class MFAInsight extends InsightBaseClass
{
    public $name = "MFA Insight";
    public $description = "Shows uptake of Multi-factor authentication";

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array
    {
        return [];
    }

    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array
    {
        //below service is referred to as $where in subsequent notes in this block for purpose of examples
        $users = AdminService::getInstance($w)->getUsers(['user.is_external' => 0, 'user.is_group' => 0, 'user.is_deleted' => 0]);

        $users_with_mfa = array_filter($users, function ($user) {
            return $user->is_mfa_enabled;
        });

        $users_without_mfa = array_filter($users, function ($user) {
            return !$user->is_mfa_enabled;
        });

        $results = [];
        $results[] = new InsightReportInterface('User MFA summary', ['# with MFA', '# without MFA'], [[count($users_with_mfa), count($users_without_mfa)]]);

        $mfa_breakdown = [];
        foreach ($users_without_mfa as $no_mfa) {
            $contact = $no_mfa->getContact();

            $mfa_breakdown[] = [
                $no_mfa->login,
                !empty($contact) ? $contact->getFullName() : 'No contact object found',
                !empty($contact) ? $contact->email : 'No contact object found',
                formatDate($no_mfa->dt_lastlogin, 'Y-m-d')
            ];
        }

        $results[] = new InsightReportInterface('No MFA breakdown', ['Login', 'Name', 'Email', 'Last Login'], $mfa_breakdown);
        return $results;
    }
}
