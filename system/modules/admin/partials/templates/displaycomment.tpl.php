<?php use Carbon\Carbon; ?>
<div id="comment_<?php echo $c->id; ?>" class="panel mt-0 flat cmfive-comment">
    <div class="comment-body">
        <div class="ps-1 ms-1">
            <img class="img-thumbnail" src='https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(@AuthService::getInstance($c->w)->getUser($c->creator_id)->getContact()->email))); ?>?d=identicon' />
        </div>
        <div style="flex: 1; margin-left: 20px; margin-right: 20px;">
            <p class="m-0">
                <strong><i class="bi bi-person me-1"></i><?php echo (!empty($c->creator_id) ? @AuthService::getInstance($c->w)->getUser($c->creator_id)->getFullName() : "") . ($c->isRestricted() ? ' <span class="fi-lock"></span>' : ""); ?></strong>
            </p>
            <?php echo $c->isRestricted() && $is_outgoing ? "[Restricted comment, please view in " . Config::get("main.application_name", "Cmfive") . "]" : CommentService::getInstance($w)->renderComment($c->comment); ?>
            <div class="text-info">
                <?php if (!empty($c->dt_created)) : ?>
                    <span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($c->dt_created, "d-M-Y \a\\t H:i"); ?>">
                        Posted <?php echo Carbon::createFromTimeStamp($c->dt_created)->diffForHumans(); ?>
                    </span>
                <?php endif;
                if (empty($displayOnly) && $external_only === false) {
                    echo HtmlBootstrap5::box(
                        href: "/admin/comment/0/{$c->getDbTablename()}/{$c->id}?internal_only=" . ($internal_only === true ? '1' : '0') . "&redirect_url=" . $redirect,
                        title: "Reply",
                        class: 'text-primary ms-3',
                        button: false,
                    );
                }
                if (AuthService::getInstance($c->w)->user()->id == $c->creator_id && empty($displayOnly)) : ?>
                    <span class="float-end">
                        <?php echo HtmlBootstrap5::box(
                            href: "/admin/comment/$c->id/$c->obj_table/$c->obj_id?internal_only=" . ($internal_only === true ? '1' : '0') . "&redirect_url=" . $redirect,
                            title: "Edit",
                            button: false,
                            class: "text-primary pe-auto"
                        );
                        echo HtmlBootstrap5::a(
                            href: "/admin/deletecomment/$c->id?redirect_url=$redirect",
                            title: "Delete",
                            confirm: "Are you sure you want to delete this comment?",
                            class: "text-danger ms-3 me-1"
                        ); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if (empty($displayOnly)) {
        echo $w->partial("loopcomments", [
            "object" => CommentService::getInstance($w)->getCommentsForTable($c->getDbTableName(), $c->id),
            "redirect" => $redirect,
            'internal_only' => $internal_only
        ], "admin");
    } ?>
</div>