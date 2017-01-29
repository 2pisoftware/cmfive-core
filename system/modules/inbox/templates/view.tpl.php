<?php
	$empty=[];
    if ($w->Auth->user()->allowed("/inbox/send")) {
        echo $w->menuButton("inbox/send/"."$message->id","Reply",$empty,'replybutton');
    }
    $empty=[];
    echo $w->menuButton("inbox/archive/".$type."/".$message->id,"Archive",$empty,'archivebutton');
    $empty=[];
    echo $w->menuButton("inbox/delete/".$type."/".$message->id,"Delete",$empty,'deletebutton');
?>
<div class='panel'>
    <h3>From: <?php echo $message->sender_id ? $message->getSender()->getFullName() : "Unknown"; ?></h3>
    <h4>Subject: <?php echo $message->subject; ?></h4>
    <h4>Date Sent: <?php echo $message->getDate("dt_created","d/m/Y H:i"); ?></h4>
</div>
<h5>Message:</h5>
<?php echo $message->getMessage(); ?>

<hr/>
<?php
$parent_id = $message->parent_message_id;

if ($parent_id) : ?>
    <div style='width: 500px; margin-bottom: 20px; padding: 10px;'>
        <b><u> Previous Messages </u></b><br/><hr/>
        <?php $counter = 1;
        while (!$parent_id == 0 || !$parent_id == null){
            if ($counter % 2 != 0){
                $bgcolor = "#ddd";
            } else {
                $bgcolor = "white";
            }
            $parent_message = $w->Inbox->getMessage($parent_id); ?>
            <div style='padding:3px; background-color: "<?php echo $bgcolor; ?>"'> Message sent by: <i><?php echo $w->Auth->getUser($parent_message->sender_id)->getFullname(); ?></i>  on: <i>" . $parent_message->getDate("dt_created","d/m/Y H:i") . "</i><br/>";
                <?php echo $parent_message->getMessage(); ?>
            </div>
            <?php $parent_id = $parent_message->parent_message_id ? $parent_message->parent_message_id : null;
            $counter++;
        } ?>
    </div>
<?php endif; ?>
