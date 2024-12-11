<div id="comments_modal_app">
    <comments-modal-component
        comment="<?php echo $comment ?>"
        comment_id="<?php echo $comment_id; ?>"
        viewers='<?php echo empty($viewers) ? json_encode([]) : $viewers; ?>'
        top_object_class_name="<?php echo $top_object_class_name; ?>"
        top_object_id="<?php echo $top_object_id; ?>"
        new_owner='<?php echo empty($new_owner) ? json_decode("[]") : $new_owner; ?>'
        can_restrict=<?php echo $can_restrict; ?>
        is_new_comment="<?php echo $is_new_comment; ?>"
        is_internal_only="<?php echo $is_internal_only; ?>"
        has_notification_selection="<?php echo $has_notification_selection; ?>"
        is_restricted="<?php echo $is_restricted; ?>"
        is_parent_restricted="<?php echo $is_parent_restricted; ?>"
        authed_user_id="<?php echo AuthService::getInstance($w)->user()->id; ?>"
    >
    </comments-modal-component>
</div>