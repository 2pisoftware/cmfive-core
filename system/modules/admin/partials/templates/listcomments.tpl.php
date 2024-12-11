<!-- var levels = ['3F7CAC', '95AFBA', 'FFCAB1', '393D3F', 'C1D7AE']; -->

<style>
    .cmfive-comment {
        padding-right: 0;
    }

    .comment-body {
        display: flex;
        align-items: center;
    }

    .cmfive-comment .comment-body {
        border-left: none;
    }

    /** level 2 */
    .cmfive-comment >
    .cmfive-comment .comment-body {
        border-left: 10px solid #3F7CAC;
    }

    /** level 3 */
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment .comment-body {
        border-left: 10px solid #95AFBA;
    }

    /** level 4 */
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment .comment-body {
        border-left: 10px solid #FFCAB1;
    }

    /** level 5 */
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment .comment-body {
        border-left: 10px solid #393D3F;
    }

    /** level 6 */
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment >
    .cmfive-comment .comment-body {
        border-left: 10px solid #C1D7AE;
    }
</style>

<?php
if (AuthService::getInstance($w)->hasRole('comment')) {
    if (!isset($internal_only)) {
        $internal_only = false;
    }
    // Shows standard comment box, url is in the form:
    // /admin/comment/[COMMENT_ID]/[TABLE_NAME]/[OBJECT_ID]?redirect_url=[REDIRECT_URL]
    // Its a bit farfetched but provides us with a standard commenting interface
    // Dont need to worry about urlencoding the redirect url
    ?>
    <div class="row mb-2">
        <div class="col-12"> 
            <?php echo HtmlBootstrap5::box(
                href: "/admin/comment/0/" . get_class($object) .
                "/{$object->id}?redirect_url=" . urlencode($redirect) .
                "&internal_only=" . ($internal_only === true ? '1' : '0') .
                "&has_notification_selection=" . ($has_notification_selection ? '1' : '0'),
                title: "Add Comment",
                button: true,
                class: "btn btn-primary"
            ); ?>
        </div>
    </div>

    <?php
    if (!empty($comments)) :
        // Now that we have comments on comments, I decided to change the partial structure
        // Loop comments is responsible for listing all of the comments given
            // It then calls displaycomment which prints and individual comment?>
        <div id="<?php echo $internal_only === true ? 'internal' : 'external'; ?>_comments_container">
            <?php echo $w->partial("loopcomments", ["object" => $comments, "redirect" => $redirect, 'internal_only' => (isset($internal_only) ? $internal_only : false), 'external_only' => (isset($external_only) ? $external_only : false)], "admin"); ?>
        </div>

        <?php CSRF::regenerate(); ?>
    <?php endif;
} else {
    ?>
    <div class="warning"><p>You do not have permissions to view and make comments. See your system administrator.</p></div>
    <?php
}
