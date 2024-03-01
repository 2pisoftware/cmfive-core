<?php

class NotificationService extends DbService
{
    const TYPE_EMAIL = 'email';

    /**
     * Sends a notification to a user
     *
     * @param string $subject
     * @param string $module
     * @param string $template_name
     * @param User $sending_user
     * @param Array $recipient_users
     * @param Array $template_data
     * @param Array $attachments
     * @return null
     */
    public function send(string $subject, string $module, string $template_name, User $sending_user, $recipient_user, array $template_data = [], array $attachments = [])
    {
        $recipient_user = $this->resolveUser($recipient_user);

        $available_methods = $this->getAvailableNotificationMethods();
        $user_preferences = $this->getUserPreferences($recipient_user);

        $usable_methods = array_intersect($user_preferences, $available_methods);

        foreach ($usable_methods as $method) {
            // Need to add template method type
            $template = TemplateService::getInstance($this->w)->findTemplate($module, $template_name);
            if (empty($template->id)) {
                LogService::getInstance($this->w)->setLogger("NOTIFICATION")->error("Template {$template_name} for module {$module} not found");
            }

            $output = TemplateService::getInstance($this->w)->render($template, $template_data);

            switch ($method) {
                case NotificationService::TYPE_EMAIL:
                default:
                    MailService::getInstance($this->w)->sendMail(
                        $recipient_user->getContact()->email,
                        ($recipient_user->is_external || empty($sending_user->id)) ? Config::get('main.company_support_email') : $sending_user->getContact()->email,
                        $subject,
                        $output,
                        null,
                        null,
                        $attachments
                    );
                    break;
            }
        }
    }

    /**
     * Sends a notification to an array of users, getting the template data from an applied callback
     *
     * @param string $subject
     * @param string $module
     * @param string $template_name
     * @param User $sending_user
     * @param Array $recipient_users
     * @param Array $template_data
     * @param Array $attachments
     * @return null
     */
    public function sendToAllWithCallback(string $subject, string $module, string $template_name, User $sending_user, array $recipient_users, callable $callback)
    {
        // Loop over users
        foreach ($recipient_users ?: [] as $recipient_user) {
            $recipient_user = $this->resolveUser($recipient_user);

            // Apply callback
            // Callback should return an instance of NotificationCallback
            $data = $callback($recipient_user, [], []);

            if (is_a($data, "NotificationCallback")) {
                $this->send($subject, $module, $template_name, $sending_user, $data->recipient_user, $data->template_data, $data->attachments);
            }
        }
    }

    /**
     * Sends a notification to an array of users
     *
     * @param string $subject
     * @param string $module
     * @param string $template_name
     * @param User $sending_user
     * @param Array $recipient_users
     * @param Array $template_data
     * @param Array $attachments
     * @return null
     */
    public function sendToAll(string $subject, string $module, string $template_name, User $sending_user, array $recipient_users, array $template_data = [], array $attachments = [])
    {
        foreach ($recipient_users ?: [] as $recipient_user) {
            $recipient_user = $this->resolveUser($recipient_user);
            $this->send($subject, $module, $template_name, $sending_user, $recipient_user, $template_data, $attachments);
        }
    }

    private function resolveUser($user)
    {
        if (!is_a($user, 'User')) {
            $user = AuthService::getInstance($this->w)->getUser(intval($user));
        }

        return $user;
    }

    /**
     * For future use
     */
    public function getUserPreferences(User $user)
    {
        return $this->getAvailableNotificationMethods();
    }

    public function getAvailableNotificationMethods()
    {
        return [
            NotificationService::TYPE_EMAIL
        ];
    }
}
