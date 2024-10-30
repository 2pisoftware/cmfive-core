<h1><?php echo $attachment->title ?></h1>
<p><?php echo $attachment->description ?></p>

<?php
if (!empty($owner))
{
    $contact = $owner->getContact();
    if (!empty($contact))
    {
        echo $contact->getFullName();
    }
    else
    {
        echo $owner->getFullName();
    }
}
?>

<p>
    Created by:
    <?php if (!empty($creator))
    {
        echo $creator->getFullName();
    } ?>
</p>

<div>
    <a
        href="/file/atfile/<?php echo $attachment->id; ?>"
        target="_blank"
        class="btn btn-primary">
        Open in new tab
    </a>

    <a
        href="/file-image/metadata/<?php echo $attachment->id; ?>"
        target="_blank"
        class="btn btn-primary">
        View metadata
    </a>
</div>

<?php if ($attachment->isImage()) : ?>
    <img
        class="img-fluid"
        data-caption="<?php echo $attachment->title; ?>"
        src="<?php echo $attachment->getThumbnailUrl(); ?>" />
<?php elseif ($attachment->isDocument()) :
    echo $attachment->getDocumentEmbedHtml();
else : ?>
    <div
        class="img-thumbnail
            bi-filetype-doc
            bi-filetype-<?php echo end(explode(".", $attachment->filename)) ?>
            text-center"
        style="font-size: 7rem; width: 210px; height: 210px;">
    </div>
<?php endif; ?>