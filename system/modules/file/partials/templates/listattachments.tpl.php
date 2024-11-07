<div class="enable_drop_attachments" data-object="<?php echo get_class($object); ?>" data-id="<?php echo $object->id; ?>" style="display:none;"></div>
<?php
if (AuthService::getInstance($w)->user()->hasRole("file_upload"))
{
    echo HtmlBootstrap5::box(
        "/file/new/" . get_class($object) . "/{$object->id}?redirect_url=" . urlencode($redirect),
        "Attach a File",
        true,
        false,
        null,
        null,
        "isbox",
        null,
        "btn btn-primary"
    );
}
?>

<?php
$url = parse_url($_SERVER["REQUEST_URI"]);
$total = FileService::getInstance($w)->countAttachments($object);
echo HtmlBootstrap5::pagination(
    $page,
    ceil($total / $page_size),
    $page_size,
    $total,
    // this is a gross hack that won't work for other pages
    // I'm sure it'll come back to bite me
    $url["path"] . "#attachments",
    "attachment__" . hash("crc32", get_class($object) . $object->id) . "__page",
);
?>

<div class="d-flex">
    <?php
    foreach ($list_items as $item) {
        echo $item;
    }
    ?>
</div>