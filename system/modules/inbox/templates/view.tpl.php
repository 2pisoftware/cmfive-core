<?php
	$empty=[];
    if ($w->Auth->user()->allowed("/inbox/send")) {
        echo $w->menuButton("inbox/send/"."$message->id",__("Reply"));
    }
    echo $w->menuButton("inbox/archive/".$type."/".$message->id,__("Archive"));
    echo $w->menuButton("inbox/delete/".$type."/".$message->id,__("Delete"));
?>
<div class='panel'>
    <h3><?php _e('From'); ?>: <?php echo $message->sender_id ? $message->getSender()->getFullName() : __("Unknown"); ?></h3>
    <h4><?php _e('Subject'); ?>: <?php echo $message->subject; ?></h4>
    <h4><?php _e('Date Sent'); ?>: <?php echo $message->getDate("dt_created","d/m/Y H:i"); ?></h4>
</div>
<h5><?php _e('Message'); ?>:</h5>
<?php echo $message->getMessage(); ?>

<hr/>
<?php
$parent_id = $message->parent_message_id;

if ($parent_id) : ?>
    <div style='width: 500px; margin-bottom: 20px; padding: 10px;'>
        <b><u> <?php _e('Previous Messages'); ?> </u></b><br/><hr/>
        <?php $counter = 1;
        while (!$parent_id == 0 || !$parent_id == null){
            if ($counter % 2 != 0){
                $bgcolor = "#ddd";
            } else {
                $bgcolor = "white";
            }
            $parent_message = $w->Inbox->getMessage($parent_id); ?>
            <div style='padding:3px; background-color: "<?php echo $bgcolor; ?>"'> <?php _e('Message sent by'); ?>: <i><?php echo $w->Auth->getUser($parent_message->sender_id)->getFullname(); ?></i>  <?php _e('on'); ?>: <i>" . $parent_message->getDate("dt_created","d/m/Y H:i") . "</i><br/>";
                <?php echo $parent_message->getMessage(); ?>
            </div>
            <?php $parent_id = $parent_message->parent_message_id ? $parent_message->parent_message_id : null;
            $counter++;
        } ?>
    </div>
<?php endif; ?>
