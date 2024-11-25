<div class="show-hover" style="max-width: 200px">
    <div>
        <h3 class="mb-0 text-center display-0 text-truncate"><?php echo $attachment->title; ?></h3>
        <!-- <p>Description: <?php echo $attachment->description; ?></p> -->
    </div>
    <div class="position-relative">
        <?php if ($attachment->isImage()) : ?>
            <img
                class="img-thumbnail"
                data-caption="<?php echo $attachment->title; ?>"
                src="<?php echo $attachment->getThumbnailUrl(); ?>" />
        <?php else : ?>
            <div
                class="img-thumbnail text-center <?php echo $attachment->getBootstrap5IconClass() ?>"
                style="font-size: 7rem; width: 200px; height: 200px;"></div>
        <?php endif; ?>

        <div class="hover-target position-absolute top-0 row ms-0 p-2" style="width: 100%; height: 100%;">
            <button
                data-modal-target="<?php echo "/file/view/" . $attachment->id . "?redirect_url=" . urlencode($redirect) ?>"
                class="btn btn-primary">
                View
            </button>

            <?php if ($is_mutable) : ?>
                <button
                    data-modal-target="<?php
                                        echo "/file/edit/" . $attachment->id
                                            . "?allowrestrictionui=" . empty($edit_attachment_restrictions_blocked)
                                            . "&redirect_url=" . urlencode($redirect) ?>"
                    class="btn btn-primary ms-0">
                    Edit
                </button>

                <?php
                echo (HtmlBootstrap5::b(
                    "/file/delete/" . $attachment->id . "?redirect_url=" . urlencode($redirect),
                    "Delete",
                    "Are you sure you want to delete this attachment?",
                    null,
                    false,
                    "btn-danger ms-0"
                ))
                ?>
            <?php endif ?>
        </div>
    </div>
</div>

<style>
    .show-hover div.hover-target {
        display: none;
    }

    .show-hover:hover div.hover-target {
        display: flex;
    }
</style>