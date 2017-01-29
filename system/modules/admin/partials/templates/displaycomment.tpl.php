<?php use Carbon\Carbon; ?>
<div id="comment_<?php echo $c->id; ?>" class="comment_section">
    <div class="comment_body clearfix">
        <div class='medium-1 column'>
            <img class='comment_avatar' src='https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(@$c->w->Auth->getUser($c->creator_id)->getContact()->email))); ?>?d=identicon' />
        </div>
        <div class='medium-11 columns comment_right_column'>
            <p><b><?php echo !empty($c->creator_id) ?@$c->w->Auth->getUser($c->creator_id)->getFullName() : ""; ?></b></p>
            <?php echo $w->Comment->renderComment($c->comment); ?>
            <div class="comment_meta">
                <?php if (!empty($c->dt_created)) : ?>
                    <span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($c->dt_created, "d-M-Y \a\\t H:i"); ?>">
                        Posted <?php echo Carbon::createFromTimeStamp($c->dt_created)->diffForHumans(); ?>
                    </span>
                <?php endif; ?>
                <?php if (empty($displayOnly)) : ?><a class="comment_reply">Reply</a><?php endif; ?>
                <?php if ($c->w->Auth->user()->id == $c->creator_id && empty($displayOnly)) : ?>
                    <span style='float: right;'>
                        <?php echo Html::box("/admin/comment/{$c->id}/{$c->obj_table}/{$c->obj_id}?redirect_url=" . $redirect, "Edit", false); ?>
                        or
                        <?php echo Html::a("/admin/deletecomment/{$c->id}?redirect_url=" . $redirect, "Delete", null, null, "Are you sure you want to delete this comment?"); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>    
    </div>
    <?php echo empty($displayOnly) ? $w->partial("loopcomments", array("object" => $w->Comment->getCommentsForTable($c->getDbTableName(), $c->id), 
                                                 "redirect" => $redirect), "admin") : ""; ?>
</div>