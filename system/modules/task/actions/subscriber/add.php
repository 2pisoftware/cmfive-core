<?php

use Html\Form\Html5Autocomplete;
use \Html\Form\InputField as InputField;

function add_GET(Web $w)
{

    $w->setLayout(null);
    list($task_id) = $w->pathMatch();

    if (empty($task_id)) {
        $w->error('Task ID not found', '/task');
    }

    $task = TaskService::getInstance($w)->getTask($task_id);
    if (empty($task->id)) {
        $w->error('Task not found', '/task');
    }

    $contacts = AuthService::getInstance($w)->getContacts();
    uasort($contacts, function ($a, $b) {
        return strcasecmp($a->getFullName(), $b->getFullName());
    });

    $w->ctx('form', HtmlBootstrap5::multiColForm([
        'Add an existing contact' => [
            [[
                new Html5Autocomplete([
                    "label" => "Contact",
                    "id|name" => "contact",
                    "placeholder" => "Search",
                    "options" => array_map(function ($contact) {
                        $user = $contact->getUser();
                        return [
                            "text" => $contact->getFullName() . ' - ' . $contact->email . (empty($user->id) || $user->is_external == 1 ? ' (external)' : ''),
                            "value" => $contact->id
                        ];
                    }, $contacts),
                ])
            ]]
        ],
        'Or add an external user' => [
            [
                (new InputField([
                    'label' => 'Firstname',
                    'id|name' => 'firstname',
                ])),
                (new InputField([
                    'label' => 'Lastname',
                    'id|name' => 'lastname',
                ])),
            ],
            [
                (new InputField([
                    'label' => 'Email',
                    'id|name' => 'email',
                    'type' => 'email',
                ])),
                (new InputField([
                    'label' => 'Phone Number',
                    'id|name' => 'work_number',
                ])),
            ],
        ],
    ], '/task-subscriber/add/' . $task_id, 'POST', 'Save', 'task-subscriber__add', null, null, "_self", true, null, false));
}

function add_POST(Web $w)
{
    $w->setLayout(null);
    list($task_id) = $w->pathMatch();

    if (empty($task_id)) {
        $w->error('Task ID not found', '/task');
    }

    $task = TaskService::getInstance($w)->getTask($task_id);
    if (empty($task->id)) {
        $w->error('Task not found', '/task');
    }

    if (!empty($_POST['contact'])) {
        $contact = AuthService::getInstance($w)->getContact(intval($_POST['contact']));
        if (empty($contact->id)) {
            $w->error('Contact not found', '/task/edit/' . $task->id);
        }

        $user = $contact->getUser();
        if (!empty($user) && !$user->is_external && !$task->canView($user)) {
            $w->error('Insufficient view permissions for user. Consider adding them to the relevant task group.', '/task/edit/' . $task->id);
        }

        $user_id = AuthService::getInstance($w)->createExernalUserForContact($contact->id);

        if ($task->addSubscriber(AuthService::getInstance($w)->getUser($user_id))) {
            $w->callHook(
                'task',
                'subscriber_notification',
                [
                    'task_id' => $task->id,
                    'user_id' => $user_id
                ]
            );
            $w->msg('Contact subscribed', '/task/edit/' . $task->id);
        } else {
            $w->msg('This contact is already a subscriber for this task', '/task/edit/' . $task->id);
        }
    } else {
        $firstname = Request::string("firstname");
        $lastname = Request::string('lastname');
        $email = Request::string('email');
        $phone = Request::string('work_number');

        if (empty($firstname) || empty($lastname) || (empty($email) || empty($phone))) {
            $w->error("All contact fields are required", '/task/edit/' . $task->id);
        }

        $contact = new Contact($w);
        $contact->firstname = $firstname;
        $contact->lastname = $lastname;
        $contact->email = $email;
        $contact->workphone = $phone;
        $contact->insert();

        $user_id = AuthService::getInstance($w)->createExernalUserForContact($contact->id);

        $task->addSubscriber(AuthService::getInstance($w)->getUser($user_id));
        $w->msg('New contact subscribed', '/task/edit/' . $task->id);
    }
}
