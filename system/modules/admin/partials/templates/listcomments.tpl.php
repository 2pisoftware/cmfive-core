<?php


    use Carbon\Carbon;

if ($w->auth->hasRole('comment')) {
    
    // Shows standard comment box, url is in the form:
    // /admin/comment/[COMMENT_ID]/[TABLE_NAME]/[OBJECT_ID]?redirect_url=[REDIRECT_URL]
    // Its a bit farfetched but provides us with a standard commenting interface
    // Dont need to worry about urlencoding the redirect url
    echo Html::box("/admin/comment/0/{$object->getDbTablename()}/{$object->id}?redirect_url=" . urlencode($redirect), __("Add Comment"), true);
    
    if (!empty($comments)) :
        // Now that we have comments on comments, I decided to change the partial structure
        // Loop comments is responsible for listing all of the comments given
            // It then calls displaycomment which prints and individual comment?>
        <div id="comments_container">
            <?php echo $w->partial("loopcomments", array("object" => $comments, "redirect" => $redirect), "admin"); ?>
        </div>

        <?php CSRF::regenerate(); ?>

    <script>

        $(document).ready(function() {
            applyColours(false);

            $(".comment_body").click(function() {
                $(this).siblings().slideToggle(200); 
            });

            $("a.comment_reply").click(function(e) {
                return comment_reply_clicked($(this));
            });
            
            <?php if (!empty($_GET['scroll_comment_id']) && is_numeric($_GET['scroll_comment_id'])) : ?>
                // Scroll to comment
                $('html, body').animate({
                    scrollTop: $("#comment_<?php echo $_GET['scroll_comment_id']; ?>").offset().top
                }, 1000);
            <?php endif; ?>
        });

        function comment_reply_clicked(element) {
            console.log("Loading reply form");
            
            $("a.comment_reply").each(function() {
               $(this).hide(); 
            });

            var closest = element.closest('.medium-11').siblings('.medium-1').first();
            var comment_section = element.closest(".comment_section");
            var comment_id = comment_section.attr('id').substr(comment_section.attr('id').indexOf('_') + 1);
            
            
            var replyForm = $('<div></div>').addClass('comment_section')
                .append($('<div></div>').addClass('comment_body clearfix')
                    .append($('<div></div>').addClass('medium-1 column')
                        .append($('<img/>').addClass('comment_avatar').attr('src', 'http://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(@$w->Auth->user()->getContact()->email))); ?>?d=identicon')))
                    .append($('<div></div>').addClass('medium-11 columns')
                        .append($('<form></form>').attr({id: 'comment_reply_form'})
                            .append($('<input>').attr({
                                type: 'hidden',
                                id: 'comment_id',
                                value: comment_id
                            }))
                            .append($('<textarea></textarea>').attr({
                                id: 'textarea_comment',
                                placeholder: __('Enter your reply...')
                            }))
                            .append($('<button>'.__('Reply').'</button>').attr({
                                type: 'submit'
                            }).addClass('button tiny'))
                            .append($('<button>'.__('Cancel').'</button>').attr({
                                type: 'button',
                                onclick: 'cancelReply($(this));'
                            }).addClass('button tiny'))
                        )
                    )
                );
//            replyForm.closest('.comment_avatar').first().attr('src', 'http://www.gravatar.com/avatar/<?php echo md5(strtolower(trim(@$w->Auth->user()->getContact()->email))); ?>?d=identicon')
            comment_section.append(replyForm);
            
            $("#textarea_comment").focus();

            $('#comment_reply_form').submit(function() {
                toggleModalLoading();
                //generate list of users to notify
                var notification_users = [];
                if ($('#is_notifications').length != 0) {
                    notification_users.push('parentObject_' + '<?php echo $object->getDbTableName(); ?>' + '_' + '<?php echo $object->id; ?>');
                    $('#notifications_list > ul > li > label').each(function () {                        
                        var notification = $('input', this);                     
                        if (notification.is(':checked')) {
                            notification_users.push(notification.attr('name'));
                        }                                                                       
                    });
                }
                
                $.ajax({
                    url  : '/admin/ajaxSaveComment/' + comment_id,
                    type : 'POST',
                    data : {
                        'redirect': '<?php echo $redirect; ?>',
                        'comment': $('#textarea_comment').val(),
                        'notification_recipients': notification_users,
                        '<?php echo \CSRF::getTokenID(); ?>': '<?php echo \CSRF::getTokenValue(); ?>'
                    },
                    complete: function(comment_response) {
                        toggleModalLoading();
                        window.location.reload();
                        
//                        cancelReply(replyForm);
//                        replyForm.remove();
//                        delete replyForm;
//                        
//                        comment_section.append(comment_response.responseText);
//                        applyColours(true);
//                        // Rebind reply links
//                        $("a.comment_reply").click(function(e) {
//                            return comment_reply_clicked($(this));
//                        }); 
                    }
                });
                return false;
            });

            return false;
        }

        function cancelReply(element) {
            element.closest('.comment_section').remove();

            $("a.comment_reply").each(function() {
                $(this).show();
            });
        }

        var levels = ['3F7CAC', '95AFBA', 'FFCAB1', '393D3F', 'C1D7AE'];
        function applyColoursToLevel(level_index, element, colours_only) {
            if (level_index < 0) {
                return;
            }

            if (level_index >= levels.length) {
                level_index = 0;
            }

            var numElements = 0;
            for (var i = 1; i < element.children().length; i++) {
                applyColoursToLevel(level_index + 1, $("#" + element.children()[i].id), colours_only);
                numElements++;
            }

            if (colours_only === false) {
                if (numElements > 0) {
                    element.children().first().find('.comment_meta > a').before("<i>(" + numElements + (numElements == 1 ? ' reply' : ' replies') + ")</i>");
                }
            }

            element.children().each(function(index) {
                if (index == 0) {
                    $(this).css('border-left', '10px solid #' + levels[level_index]);
                }
            });
        }

        function applyColours(colours_only) {
            $.each($("#comments_container > .comment_section"), function(index) {
                var numElements = 0;
                for (var i = 1; i < $(this).children().length; i++) {
                    applyColoursToLevel(0, $("#" + $(this).children()[i].id), colours_only);
                    numElements++;
                }

                if (colours_only === false) {
                    if (numElements > 0) {
                        $(this).children().first().find('.comment_meta > a').before("<i>(" + numElements + (numElements == 1 ? __(' reply') : __(' replies')) + ")</i>");
                    }
                }
            });
       }

    </script>
<?php endif;
