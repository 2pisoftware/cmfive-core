<?php

/**
 * Generic get form application function for vue endpoints
 * Named as such to prevent collisions
 *
 * @param  \Web   $w web object
 * @return FormApplication|null
 */
function getFormApplication_VUE(Web $w)
{
    list($id) = $w->pathMatch('id');

    if (empty($id)) {
        throw new \Exception('Missing ID');
    }

    $application = FormApplicationService::getInstance($w)->getFormApplication($id);
    if (empty($application->id)) {
        throw new \Exception('Application not found');
    }

    return $application;
}

function getResponse_VUE()
{
    return ['success' => false, 'error' => '', 'data' => []];
}

function save_application_POST(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();
    $application = null;

    try {
        $application = getFormApplication_VUE($w);
    } catch (Exception $e) {
        $ouput['error'] = $e->getMessage();
        $w->out(json_encode($output));
        return;
    }

    $is_active = Request::string('is_active');
    $application->title = Request::string('title');
    $application->description = Request::string('description');
    $application->is_active = ($is_active === "true" || intval($is_active) === 1 ? 1 : 0);

    $application->update();

    $output['success'] = true;
    $w->out(json_encode($output));
}

function get_members_GET(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();
    $application = null;

    try {
        $application = getFormApplication_VUE($w);
    } catch (Exception $e) {
        $ouput['error'] = $e->getMessage();
        $w->out(json_encode($output));
        return;
    }

    $members = $application->getMembers();
    if (!empty($members)) {
        foreach ($members as $member) {
            $output['data'][] = [
                'id' => $member->id,
                'member_user_id' => $member->member_user_id,
                'name' => $member->getName(),
                'role' => $member->role,
                'application_id' => $member->application_id
            ];
        }
    }

    $output['success'] = true;
    $w->out(json_encode($output));
}

function get_forms_GET(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();
    $application = null;

    try {
        $application = getFormApplication_VUE($w);
    } catch (Exception $e) {
        $ouput['error'] = $e->getMessage();
        $w->out(json_encode($output));
        return;
    }

    $forms = $application->getForms();
    if (!empty($forms)) {
        foreach ($forms as $form) {
            $output['data'][] = [
                'id' => $form->id,
                'title' => $form->title,
                'no_instances' => $form->countFormInstancesForObject($application)
            ];
        }
    }

    $output['success'] = true;
    $w->out(json_encode($output));
}

function get_form_instances_GET(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();
    list($form_id, $object_type, $object_id) = $w->pathMatch('form_id', 'object_type', 'object_id');

    // Check parameters
    if (empty($form_id) || empty($object_type) || empty($object_id)) {
        $output['message'] = 'Requried parameters not found';
        $w->out(json_encode($output));
        return;
    }

    // Get form
    $form = FormService::getInstance($w)->getForm($form_id);
    if (empty($form->id)) {
        $output['message'] = 'Form not found';
        $w->out(json_encode($output));
        return;
    }

    // Get Object
    $object = FormService::getInstance($w)->getObject($object_type, $object_id);

    if (empty($object->id)) {
        $output['message'] = 'Object not found';
        $w->out(json_encode($output));
        return;
    }

    $output['data'] = array_map(function ($instance) {
        return $instance->toArray();
    }, FormService::getInstance($w)->getFormInstancesForFormAndObject($form, $object) ?: []);

    $output['success'] = true;
    $w->out(json_encode($output));
}

function save_form_POST(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();

    // Validate data
    if (empty($_POST['application_id']) || empty($_POST['id'])) {
        $output['error'] = 'Missing data';
        $w->out(json_encode($output));
        return;
    }

    $application_id = intval($_POST['application_id']);
    $form_id = intval($_POST['id']);

    // Get application and validate
    $application = FormApplicationService::getInstance($w)->getFormApplication($application_id);
    if (empty($application->id)) {
        $output['error'] = 'Application not found';
        $w->out(json_encode($output));
        return;
    }

    // Get form and validate
    $form = FormService::getInstance($w)->getForm($form_id);
    if (empty($form->id)) {
        $output['error'] = 'Form not found';
        $w->out(json_encode($output));
        return;
    }

    // Validate no existing mapping
    $existing_mapping = FormApplicationService::getInstance($w)->getFormApplicationMapping($application_id, $form_id);
    if (empty($existing_mapping->id)) {
        $mapping = new FormApplicationMapping($w);
        $mapping->application_id = $application_id;
        $mapping->form_id = $form_id;

        if (array_key_exists("type", $_POST) && $_POST["type"] === "single") {
            $mapping->is_singleton = true;
        }

        $mapping->insert();
    }

    // Return
    $output['success'] = true;
    $w->out(json_encode($output));
}

function save_member_POST(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();

    // Validate data
    if (empty($_POST['application_id']) || empty($_POST['member_user_id']) || empty($_POST['role'])) {
        $output['error'] = 'Missing data';
        $w->out(json_encode($output));
        return;
    }

    $application_id = intval($_POST['application_id']);
    $member_user_id = intval($_POST['member_user_id']);

    $existing_record_id = '';
    if (!empty($_POST['id'])) {
        $existing_record_id = intval($_POST['id']);
    }

    // Get application and validate
    $application = FormApplicationService::getInstance($w)->getFormApplication($application_id);
    if (empty($application->id)) {
        $output['error'] = 'Application not found';
        $w->out(json_encode($output));
        return;
    }

    // Get user and validate
    $user = AuthService::getInstance($w)->getUser($member_user_id);
    if (empty($user)) {
        $output['error'] = 'User not found';
        $w->out(json_encode($output));
        return;
    }

    // Validate role
    if (!in_array($_POST['role'], FormApplicationMember::$_roles)) {
        $output['error'] = 'Invalid role';
        $w->out(json_encode($output));
        return;
    }

    // Find/create
    if (!empty($existing_record_id)) {
        $application_member = FormApplicationService::getInstance($w)->getObject("FormApplicationMember", $existing_record_id);
        if (empty($application_member->id)) {
            $output['error'] = 'Existing record not found';
            $w->out(json_encode($output));
            return;
        } else {
            $application_member->member_user_id = $member_user_id;
            $application_member->role = Request::string('role');
            $application_member->update();
        }
    } else {
        $application_member = new FormApplicationMember($w);
        $application_member->application_id = $application_id;
        $application_member->member_user_id = $member_user_id;
        $application_member->role = Request::string('role');
        $application_member->insert();
    }

    // Return
    $output['success'] = true;
    $w->out(json_encode($output));
}

function delete_form_GET(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();

    list($id, $form_id) = $w->pathMatch('id', 'form_id');

    if (empty($id) || empty($form_id)) {
        $output['error'] = 'Missing ID';
        $w->out(json_encode($output));
        return;
    }

    $application = FormApplicationService::getInstance($w)->getFormApplication($id);
    if (empty($application->id)) {
        $output['error'] = 'Application not found';
        $w->out(json_encode($output));
        return;
    }

    $existing_mapping = FormApplicationService::getInstance($w)->getFormApplicationMapping($application->id, $form_id);
    if (!empty($existing_mapping->id)) {
        $existing_mapping->delete();
    }

    $output['success'] = true;
    $w->out(json_encode($output));
}

function delete_member_GET(Web $w)
{
    $w->setLayout(null);

    $output = getResponse_VUE();

    list($id, $member_id) = $w->pathMatch('id', 'member_id');

    if (empty($id) || empty($member_id)) {
        $output['error'] = 'Missing ID';
        $w->out(json_encode($output));
        return;
    }

    $application = FormApplicationService::getInstance($w)->getFormApplication($id);
    if (empty($application->id)) {
        $output['error'] = 'Application not found';
        $w->out(json_encode($output));
        return;
    }

    $existing_member = FormApplicationService::getInstance($w)->getObject('FormApplicationMember', $member_id);
    if (!empty($existing_member->id) && $existing_member->application_id == $id && $existing_member->is_deleted == 0) {
        $existing_member->delete();
    }

    $output['success'] = true;
    $w->out(json_encode($output));
}

function get_form_instance_rows_GET(Web $w)
{
    $output = getResponse_VUE();

    list($form_id, $object, $object_id) = $w->pathMatch('form_id', 'object', 'object_id');
    $page = intval(Request::string('page')) - 1;
    $pagesize = intval(Request::string('pagesize'));
    $display_only = intval(Request::string('display_only'));
    $redirect_url = Request::string('redirect_url');

    // Check object class
    if (!class_exists($object)) {
        $output['error'] = 'Object not found';
        $w->out(json_encode($output));
        return;
    }

    // Validate objects
    $form = FormService::getInstance($w)->getForm($form_id);
    $_object = FormService::getInstance($w)->getObject($object, $object_id);

    if (empty($form->id) || empty($_object->id)) {
        $output['error'] = 'Form or object not found';
        $w->out(json_encode($output));
        return;
    }

    $instances = FormService::getInstance($w)->getFormInstancesForFormAndObject($form, $_object, ($page * $pagesize), $pagesize);
    $instances_array = [];
    if (!empty($instances)) {
        foreach ($instances as $instance) {
            $row_text = $instance->getTableRow();
            if (!$display_only) {
                $row_text .= '<td>' .
                    HtmlBootstrap5::box("/form-instance/edit/" . $instance->id . "?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($_object) . "&object_id=" . $_object->id, "Edit", true) .
                    HtmlBootstrap5::b("/form-instance/delete/" . $instance->id . "?redirect_url=" . $redirect_url, "Delete", "Are you sure you want to delete this item?", null, false, 'warning') .
                    '</td>';
            }

            $instances_array[] = $row_text;
        }
    }

    $output['success'] = true;
    $output['data'] = $instances_array;
    $w->out(json_encode($output, true));
}
