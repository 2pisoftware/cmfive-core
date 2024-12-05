<?php

function manage_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    list($id) = $w->pathMatch('id');

    $application = null;
    if (empty($id)) {
        $application = new FormApplication($w);
        $application->insert();
        $w->redirect('/form-application/edit/' . $application->id);
    } else {
        $application = FormService::getInstance($w)->getFormApplication($id);
    }

    $members = $application->getMembers();

    $memberTableHeaders = ['User', 'Role', 'Actions'];
    $memberTableData = [];
    if (!empty($members)) {
        foreach ($members as $member) {
            $memberTableData[] = [
                '<span class="text-break">' . $member->getName() . '</span>',
                $member->role,
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::box("/form-application/edit_member/$application->id?member=$member->id", 'Edit', class: 'btn btn-primary') .
                    HtmlBootstrap5::b("/form-application/delete_member/$application->id?member=$member->id", 'Delete', 'Are you sure you want to delete this member?', class: 'btn btn-danger'),
                )
            ];
        }
    }

    $forms = $application->getForms();
    $formTableHeaders = ['Form', '# saved rows', 'Actions'];
    if (!empty($forms)) {
        foreach ($forms as $form) {
            $formTableData[] = [
                '<span class="text-break">' . $form->title . '</span>',
                $form->countFormInstancesForObject($application) ?: 0,
                HtmlBootstrap5::b("/form-application/delete_form/$application->id?form=$form->id", 'Delete', 'Are you sure you want to delete this form?', class: 'btn btn-sm btn-danger'),
            ];
        }
    }

    $w->ctx('application', $application);
    $w->ctx('members', HtmlBootstrap5::table($memberTableData, null, "tablesorter", $memberTableHeaders));
    $w->ctx('attached_forms', HtmlBootstrap5::table($formTableData, null, "tablesorter", $formTableHeaders));
}