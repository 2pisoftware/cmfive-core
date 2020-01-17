<?php

use \Html\Form\InputField as InputField;

function add_GET(Web $w)
{

    $w->setLayout(null);
    list($task_id) = $w->pathMatch();

    if (empty($task_id)) {
        $w->error('Task ID not found', '/task');
    }

    $task = $w->Task->getTask($task_id);
    if (empty($task->id)) {
        $w->error('Task not found', '/task');
    }

    $contacts = $w->Auth->getContacts();
    uasort($contacts, function ($a, $b) {
        return strcasecmp($a->getFullName(), $b->getFullName());
    });

    $w->ctx('form', Html::multiColForm([
        'Add an existing contact' => [
            [[(new \Html\Form\Autocomplete())->setLabel('Contact')
                    ->setId('contact')
                    ->setName('contact')
                    ->setOptions($contacts, function ($contact) {
                        $user = $contact->getUser();
                        return $contact->getFullName() . ' - ' . $contact->email . (empty($user->id) || $user->is_external == 1 ? ' (external)' : '');
                    }),
            ]],
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
    ], '/task-subscriber/add/' . $task_id, 'POST', 'Save', 'task-subscriber__add'));
}

function add_POST(Web $w)
{

    $w->setLayout(null);
    list($task_id) = $w->pathMatch();

    if (empty($task_id)) {
        $w->error('Task ID not found', '/task');
    }

    $task = $w->Task->getTask($task_id);
    if (empty($task->id)) {
        $w->error('Task not found', '/task');
    }

    if (!empty($_POST['contact'])) {
        $contact = $w->Auth->getContact(intval($_POST['contact']));
        if (empty($contact->id)) {
            $w->error('Contact not found', '/task/edit/' . $task->id);
        }

        $user_id = $w->Auth->createExernalUserForContact($contact->id);

        $existing_subscription = $w->Task->getObject("TaskSubscriber", ['task_id' => $task->id, "user_id" => $user_id, "is_deleted" => 0]);

        if (empty($existing_subscription->id)) {
            $subscription = new TaskSubscriber($w);
            $subscription->task_id = $task->id;
            $subscription->user_id = $user_id;
            $subscription->insert();

            $w->msg('Contact subscribed', '/task/edit/' . $task->id);
        } else {
            $w->msg('This contact is already a subscriber for this task', '/task/edit/' . $task->id);
        }
    } else {
        $firstname = $w->request("firstname");
        $lastname = $w->request('lastname');
        $email = $w->request('email');
        $phone = $w->request('work_number');

        if (empty($firstname) || empty($lastname) || (empty($email) || empty($phone))) {
            $w->error("All contact fields are required", '/task/edit/' . $task->id);
        }

        $contact = new Contact($w);
        $contact->firstname = $firstname;
        $contact->lastname = $lastname;
        $contact->email = $email;
        $contact->workphone = $phone;
        $contact->insert();

        $user_id = $w->Auth->createExernalUserForContact($contact->id);

        $subscription = new TaskSubscriber($w);
        $subscription->task_id = $task->id;
        $subscription->user_id = $user_id;
        $subscription->insert();

        $w->msg('New contact subscribed', '/task/edit/' . $task->id);
    }
}
