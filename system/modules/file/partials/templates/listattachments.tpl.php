<div class="enable_drop_attachments" data-object="<?php echo get_class($object); ?>" data-id="<?php echo $object->id; ?>" style="display:none;"></div>
<?php
if (AuthService::getInstance($w)->user()->hasRole("file_upload")) {
    echo Html::box("/file/new/" . get_class($object) . "/{$object->id}?redirect_url=" . urlencode($redirect), "Attach a File", true);
}
?>
<div id="image_attachment_list">
    <?php echo Html::paginatedList(
        $list_items,
        $page,
        $page_size,
        FileService::getInstance($w)->countAttachments($object),
        $redirect,
        null,
        "asc",
        "attachment__" . hash("crc32", get_class($object) . $object->id) . "__page",
        "attachment__page-size"
    ); ?>
</div>
<script>
    $(document).ready(function() {
        $(".image-container-overlay button").removeClass("tiny");
        $(".image-container").hover(function() {
            $(".image-container-overlay", this).stop().fadeIn("fast");
        }, function() {
            $(".image-container-overlay", this).stop().fadeOut("fast");
        });
    });
</script>
<style>
    div.image-container {
        width: auto;
        height: 220px;
        position: relative;
        margin-left: auto;
        margin-right: auto;
        overflow: hidden;
        padding: 10px;
        border: 1px solid #444;
        margin: 5px;
        background-color: #efefef;
    }

    div.image-container-overlay {
        background-color: rgba(0, 0, 0, 0.7);
        position: absolute;
        margin: auto;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 100;
        display: none;
        padding: 10px;
    }

    div.image-container-overlay>.row-fluid {
        height: 75px;
    }

    div.image-container-overlay:hover {
        display: block;
    }

    img.image-cropped {
        position: absolute;
        margin: auto;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }
</style>