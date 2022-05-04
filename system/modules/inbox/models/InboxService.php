<?php
class InboxService extends DbService {

    function addMessage($subject, $message, $user_id = null, $sender_id = null, $parent_id = null, $send_email = true) {
        $logged_in = !!AuthService::getInstance($this->w)->loggedIn();
        if (!$user_id) {
            $user_id = $logged_in ? AuthService::getInstance($this->w)->user()->id : null;
        }
        if (!$sender_id) {
            $sender_id = $logged_in ? AuthService::getInstance($this->w)->user()->id : null;
        }
        if (!is_a($message, "DbObject")) {
            $mso = new Inbox_message($this->w);
            $mso->message = $message;
            $mso->insert();
        } else {
            $mso = $message;
        }
        $msg = new Inbox($this->w);
        $msg->user_id = $user_id;
        $msg->parent_message_id = $parent_id;
        $msg->subject = $subject;
        if ($sender_id) {
            $msg->sender_id = $sender_id;
        }
        $msg->message_id = $mso->id;
        $msg->dt_created = time();
        $msg->is_new = 1;
        $msg->is_archived = 0;
        $msg->insert();
    }

    function sendMail($to, $cc, $bcc, $from, $replyto, $subject, $message) {
        LogService::getInstance($this->w)->info("Inbox service is asserting retired mail system");
        $mailconf = Config::get('inbox.phpmailer');//$this->w->moduleConf("inbox", "phpmailer");

        if ($mailconf) {
            require_once('PHPMailer/class.phpmailer.php');
            $mail = new PHPMailer();
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = $mailconf['Host']; // SMTP server
            $mail->SMTPDebug = 1;                     // enables SMTP debug information (for testing)
            // 1 = errors and messages
            // 2 = messages only
            $mail->SMTPAuth = $mailconf['SMTPAuth'];                  // enable SMTP authentication
            $mail->SMTPSecure = $mailconf['SMTPSecure'];                 // sets the prefix to the servier
            $mail->Host = $mailconf['Host'];      // sets GMAIL as the SMTP server
            $mail->Port = $mailconf['Port'];                   // set the SMTP port for the GMAIL server
            $mail->Username = $mailconf['Username'];  // GMAIL username
            $mail->Password = $mailconf['Password'];            // GMAIL password

            if ($from) {
                $mail->SetFrom($from);
            } else {
                $mail->SetFrom($mailconf['Username']);
            }

            if ($replyto) {
                $mail->AddReplyTo($replyto);
            }

            if ($subject) {
                $mail->Subject = $subject;
            }

            if ($message) {
                $mail->AltBody = $message; // optional, comment out and test
                $mail->MsgHTML($message);
            }

            // add TO address(es)
            if ($to != null && is_array($to)) {
                foreach ($to as $a) {
                    $mail->AddAddress($a);
                }
            } elseif ($to) {
                $mail->AddAddress($to);
            }

            // add CC address(es)
            if ($cc != null && is_array($cc)) {
                foreach ($cc as $a) {
                    $mail->AddCC($a);
                }
            } elseif ($cc) {
                $mail->AddCC($cc);
            }

            // add BCC address(es)
            if ($bcc != null && is_array($bcc)) {
                foreach ($bcc as $a) {
                    $mail->AddBCC($a);
                }
            } elseif ($bcc) {
                $mail->AddBCC($bcc);
            }
			try {
				if (!$mail->Send()) {
					$this->w->error("Mailer Error: " . $mail->ErrorInfo, "/main/index");
					return false;
				}
			} catch (Exception $e) {
				$this->w->error("Mailer Error: " . $e, "/main/index");
			}
            return true;
        }
    }

    function inboxCountMarker() {
        $user_id = AuthService::getInstance($this->w)->user()->id;
        $count_messages = $this->_db->get("inbox")->where("user_id", $user_id)->where("is_new", 1)->where("is_deleted", 0)->count();
        return ($count_messages > 0) ? "<span class='label secondary round' style='margin-left: 5px;'>" . $count_messages . "</span>" : "";
    }

    function getMessages($page, $page_size, $user_id, $is_new, $is_arch = 0, $is_del = 0, $has_parent = 0) {
        $offset = $page * $page_size;
        $rows = $this->_db->get("inbox")->where("user_id", $user_id)
                    ->where("is_new", $is_new)->where("is_deleted", $is_del)
                    ->where("has_parent", $has_parent)->where("del_forever", 0);
        if ($is_arch !== 0 and $is_del !== 1) {
            $rows->where("is_archived", $is_arch);
        }
        $rows->orderBy("dt_created")->limit($offset, $page_size);
        return $this->fillObjects("Inbox", $rows->fetchAll());
    }

    function getDelMessageCount() {
        $user_id = AuthService::getInstance($this->w)->user()->id;
        return $this->_db->get('inbox')->where("is_deleted", 1)->where("user_id", $user_id)
                    ->where("del_forever", 0)->count();
    }

    function getNewMessageCount() {
        // Get logged in user
        $user_id = AuthService::getInstance($this->w)->User()->id;
        if (empty($user_id)) {
            return 0;
        }
        // Get number of messages PROPERLY
        $count = $this->_db->get('inbox')->where('is_deleted', 0)->where('is_new', 1)
                        ->where('is_archived', 0)->where("user_id", $user_id)
                        ->where("del_forever", 0)->count();
        return $count;
    }

    function getReadMessageCount() {
        // Get logged in user
        $user_id = AuthService::getInstance($this->w)->User()->id;
        if (empty($user_id)) {
            return 0;
        }
        // Get number of messages PROPERLY
        $count = $this->_db->get('inbox')->where('is_deleted', 0)->where('is_new', 0)
                        ->where('is_archived', 0)->where("user_id", $user_id)
                        ->where("del_forever", 0)->count();
        return $count;
    }

    function getArchCount() {
        $user_id = AuthService::getInstance($this->w)->user()->id;
        $new_count = $this->_db->get('inbox')->where("is_deleted", 0)->where("is_new", 1)
                                ->where("is_archived", 1)->where("user_id", $user_id)
                                ->where("del_forever", 0)->count();
     
        $arch_count = $this->_db->get('inbox')->where("is_deleted", 0)->where("is_new", 0)
                                ->where("is_archived", 1)->where("user_id", $user_id)
                                ->where("del_forever", 0)->count();

        return ($new_count + $arch_count);
    }

    function getMessage($id) {
        return $this->getObject("Inbox", $id);
    }

    function notifyRoleUsers($role, $subject, $message, $sender_id = null) {
        $users = AuthService::getInstance($this->w)->getUsersForRole($role);

        // no notification for current user:
        $logged_uid = AuthService::getInstance($this->w)->user()->id;

        while (!is_null($key = key($users))) {

            if ($users[$key]->id == $logged_uid)
                unset($users[$key]);

            next($users);
        }


        // notify the rest:
        if ($users) {
            $mso = new Inbox_message($this->w);
            $mso->message = $message;
            $mso->insert();


            foreach ($users as $u) {
                $this->addMessage($subject, $mso, $u->id, $sender_id);
            }
        }
    }
    
    function markAllMessagesRead() {
        return $this->_db->update("inbox", array("is_new" => 0, "dt_read" => formatDate(time(), "Y-m-d H:i:s")))
                ->where("user_id", AuthService::getInstance($this->w)->user()->id)->where("is_new", 1)->execute();
    }

    public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }
        $nav = $nav ? $nav : array();
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("inbox", "New Messages", $nav);
            $w->menuLink("inbox/read", "Read Messages", $nav);
            $w->menuLink("inbox/showarchive", "Archive", $nav);
            $w->menuLink("inbox/trash", "Bin", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

    function menuLink() {
        return AuthService::getInstance($this->w)->allowed("/inbox", 
            Html::a($this->w->localUrl("/inbox"), "Inbox" . $this->inboxCountMarker(), "Inbox", "current active")
        );
    }
    
}
