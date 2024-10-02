<?php if (AuthService::getInstance($w)->user()->hasRole('tag_user') && $object->canView(AuthService::getInstance($w)->user())) : ?>

    <style>
        .hidden_tags {
            display: none;
        }

        .tags-container {
            display: flex;
            align-items: center;
            margin-left: 0.5rem;
            display: inline-block;
        }

        .tags-container > #hidden_tags {
            display: inline;
        }

        .tag-small {
            font-size: 0.7rem;
            padding: 2px 3px 2px 3px;
            margin-left: 5px;
        }
    </style>

    <div class="tags-container" data-tag-id="<?php echo $tag_obj_id ?>" data-modal-target="/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>">
        <div id="tags_<?php echo $tag_obj_id ?>">
        </div>

        <div id="hidden_tags_<?php echo $tag_obj_id ?>" class="hidden_tags">
        </div>
    </div>
<?php endif;
