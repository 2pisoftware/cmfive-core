<?php namespace System\Modules\Tag;

function listTags(\Web $w, $params = []) {
    
    if (empty($params['object'])) {
        return;
    }
    
<<<<<<< HEAD
    \CmfiveStyleComponentRegister::registerComponent("selectize-css", (new \CmfiveStyleComponent("/composer/vendor/grimmlink/selectize/dist/css/selectize.css", ['weight' => 300])));
    \CmfiveStyleComponentRegister::registerComponent("tag-css", (new \CmfiveStyleComponent("/system/modules/tag/assets/css/style.css", ['weight' => 290])));
    \CmfiveScriptComponentRegister::registerComponent("selectize-js", (new \CmfiveScriptComponent("/composer/vendor/grimmlink/selectize/dist/js/standalone/selectize.js", ['weight' => 300])));
    
=======
>>>>>>> d5ecc1ea89560d42fb3a9fe87546de675e2e6a62
    $w->ctx('object', $params['object']);
    
    // Filter tags into a displayable group and a group that only shows on hover
    $tags = \TagService::getInstance($w)->getTagsByObject($params['object']);
    $filtered_tags = ['display' => [], 'hover' => []];
    
    $tag_str_len = 0;
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            if ($tag_str_len < 10) {
                $filtered_tags['display'][] = ['id' => $tag->id, 'tag' => $tag->tag];
            } else {
                $filtered_tags['hover'][] = ['id' => $tag->id, 'tag' => $tag->tag];
            }
            
            $tag_str_len += strlen($tag->tag);
        }
    }
    
    $w->ctx('tags', $filtered_tags);

    $object = $params["object"];
    $w->ctx("tag_obj_id", get_class($object) . "_" . $object->id);
}