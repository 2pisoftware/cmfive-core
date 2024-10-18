<div class="text-center">
    <h3>Tags for <span><?php echo $object_class; ?>: <?php echo $object->getSelectOptionTitle(); ?>
        </span></h3>

    <?php

    use Html\Form\Html5Autocomplete;

    echo new Html5Autocomplete([
        'id' => 'display_tags_' . $object_class . '_' . $id,
        "placeholder" => "Add tags...",
        'name' => 'tags',
        "maxItems" => null,
        "canCreate" => true,
        "options" => array_map(function ($val) use ($object_tags, $object_class) {
            return [
                "value" => $val->getSelectOptionValue(),
                "text" => $val->getSelectOptionTitle(),
                "type" => empty($object_tags)
                    ? "all"
                    // will throw an error if object_tags has no elements
                    : (array_search($val->id, array_column($object_tags, "id")) !== false
                        ? $object_class
                        : "all")
            ];
        }, $all_tags),
        "groups" => [
            [ "value" => $object_class, "label" => "Existing tags on a " . $object_class],
            [ "value" => "all", "label" => "All tags" ],
        ],
        "value" => array_map(fn ($val) => $val->id, $tags),

        "plugins" => ["remove_button"],

        // onItemAdd is called for both creation and addition,
        // and ajaxCreateTag creates if not exists, and adds it to the object if not already
        "onItemAdd" => $w->localUrl("/tag/ajaxAddTag/{$object_class}/{$id}"),
        "onItemCreate" => $w->localUrl("/tag/ajaxCreateTag/{$object_class}/{$id}"),
        "onItemRemove" => $w->localUrl("/tag/ajaxRemoveTag/{$object_class}/{$id}"),
    ]);
    ?>
</div>

<style>
    #cmfive-modal .ts-control {
        padding: 10px;
    }

    #cmfive-modal .ts-dropdown-content {
        display: flex;
        flex-wrap: wrap;
    }

    #cmfive-modal .optgroup {
        flex: 1;
    }

    #cmfive-modal .optgroup-header {
        border-bottom: 1px solid black;
    }

    #cmfive-modal .create {
        width: 100%;
    }
</style>