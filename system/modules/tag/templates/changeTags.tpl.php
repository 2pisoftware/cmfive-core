<div class="text-center">
    <h3>Tags for <span><?php echo $object_class; ?>: <?php echo $object->getSelectOptionTitle(); ?>
        </span></h3>

    <?php

    use Html\Form\Html5Autocomplete;

    echo new Html5Autocomplete([
        'id' => 'display_tags_' . $object_class . '_' . $id,
        'name' => 'tags',
        "maxItems" => null,
        "canCreate" => true,
        "options" => implode(',', array_map(function ($tag) {
            return $tag->id;
        }, $tags ?: [])),
        // onItemAdd is called for both creation and addition,
        // and ajaxCreateTag creates if not exists, and adds it to the object if not already
        "onItemAdd" => $w->localUrl("/tag/ajaxCreateTag/{$object_class}/{$id}"),
        "onItemRemove" => $w->localUrl("/tag/ajaxRemoveTag/{$object_class}/{$id}"),
    ]);
    ?>
</div>