<?php
class InboxService extends DbService {

    function addMessage($subject, $message, $user_id = null, $sender_id = null, $parent_id = null, $send_email = true) {
        $logged_in = !!$this->Auth->loggedIn();
        if (!$user_id) {
            $user_id = $logged_in ? $this->Auth->user()->id : null;
        }
        if (!$sender_id) {
            $sender_id = $logged_in ? $this->Auth->user()->id : null;
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

        $receiver = $this->Auth->getUser($user_id);
        
        // Notify users via email if specified and the user isn't sending a message to themselves
        // $this->w->Log->debug("IDs: " . var_export($msg->user_id, true) . " - " . var_export($msg->sender_id, true));
        if (!empty($mso) && !empty($msg) && !empty($receiver)) {
			$rContact=$receiver->getContact();
			$lSender=$this->w->Auth->getUser($msg->sender_id);
			if (!empty($rContact) && !empty($lSender)) {
				$lContact=$lSender->getContact();
				if (!empty($lContact) && $send_email === true && $msg->user_id !== $msg->sender_id) {
					$this->w->Mail->sendMail($rContact->email, $logged_in ? $lContact->email : Config::get('main.company_support_email'), $msg->subject, $mso->message);
				}
			}
		}
    }

    function sendMail($to, $cc, $bcc, $from, $replyto, $subject, $message) {
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
        $user_id = $this->w->Auth->user()->id;
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
        $rows->order_by("dt_created")->limit($offset, $page_size);
        return $this->fillObjects("Inbox", $rows->fetch_all());
    }

    function getDelMessageCount() {
        $user_id = $this->w->Auth->user()->id;
        $count = $this->_db->get('inbox')->where("is_deleted", 1)->where("user_id", $user_id)
                    ->where("del_forever", 0)->count();
//        $sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 1 AND user_id = " . $user . " AND del_forever = 0";
//        $result = $this->_db->sql($sql)->fetch_row();
//        $result ? $result = $result['COUNT(*)'] : $result = 0;
        return $count;
    }

    function getNewMessageCount() {
        // Get logged in user
        $user_id = $this->w->Auth->User()->id;
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
        $user_id = $this->w->Auth->User()->id;
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
        $user_id = $this->w->Auth->user()->id;
        $new_count = $this->_db->get('inbox')->where("is_deleted", 0)->where("is_new", 1)
                                ->where("is_archived", 1)->where("user_id", $user_id)
                                ->where("del_forever", 0)->count();
        //$sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 1 AND is_archived = 1 AND user_id = " . $user . " AND del_forever = 0";
//        $newarch = $this->_db->sql($sql)->fetch_row();
//        $newarch ? $newarch = $newarch['COUNT(*)'] : $newarch = 0;
        $arch_count = $this->_db->get('inbox')->where("is_deleted", 0)->where("is_new", 0)
                                ->where("is_archived", 1)->where("user_id", $user_id)
                                ->where("del_forever", 0)->count();
//        $sql = "SELECT COUNT(*) FROM `inbox` WHERE is_deleted = 0 AND is_new = 0 AND is_archived = 1 AND user_id = " . $user . " AND del_forever = 0";
//        $arch = $this->_db->sql($sql)->fetch_row();
//        $arch ? $arch = $arch['COUNT(*)'] : $arch = 0;
//        $total = ($newarch * 1) + ($arch * 1);
        return ($new_count + $arch_count);
    }

    function getMessage($id) {
        return $this->getObject("Inbox", $id);
    }

    function notifyRoleUsers($role, $subject, $message, $sender_id = null) {
        $users = $this->Auth->getUsersForRole($role);

        // no notification for current user:
        $logged_uid = $this->w->Auth->user()->id;

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
        $user_id = $this->Auth->user()->id;
        return $this->_db->update("inbox", array("is_new" => 0, "dt_read" => time()))
                ->where("user_id", $user_id)->where("is_new", 1)->execute();
//        return $this->_db->sql("update inbox set is_new = 0, dt_read = NOW() where user_id = $user_id and is_new = 1")->execute();
    }

    public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }
        $nav = $nav ? $nav : array();
        if ($w->Auth->loggedIn()) {
            $w->menuLink("inbox", __("New Messages"), $nav);
            $w->menuLink("inbox/read", __("Read Messages"), $nav);
            $w->menuLink("inbox/showarchive", __("Archive"), $nav);
            $w->menuLink("inbox/trash", __("Bin"), $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

    function menuLink() {
        return $this->w->Auth->allowed("/inbox", 
            Html::a($this->w->localUrl("/inbox"), "Inbox" . $this->inboxCountMarker(), __("Inbox"), "current active")
        );
    }
    
}
