<?php
namespace System\Modules\Tag;



function listTags(\Web $w, $params = [])
{
    if (empty($params['object'])) {
        return;
    }

    // Ensure selectize is accessible
    if (!file_exists(PUBLIC_PATH . "composer/vendor/grimmlink/selectize/dist/css/selectize.css")) {
        copy(ROOT_PATH . "composer/vendor/grimmlink/selectize/dist/css/selectize.css", PUBLIC_PATH . "cache/css/grimmlink_selectize.css");
    }

    if (!file_exists(PUBLIC_PATH . "composer/vendor/grimmlink/selectize/dist/js/standalone/selectize.js")) {
        copy(ROOT_PATH . "composer/vendor/grimmlink/selectize/dist/js/standalone/selectize.js", PUBLIC_PATH . "cache/css/grimmlink_selectize.js");
    }

    $w->enqueueStyle(['name' => 'selectize-css', 'uri' => '/cache/css/grimmlink_selectize.css', 'weight' => 300]);
    $w->enqueueStyle(['name' => 'tag-css', 'uri' => '/system/modules/tag/assets/css/style.css', 'weight' => 290]);
    $w->enqueueScript(['name' => 'selectize-js', 'uri' => '/cache/css/grimmlink_selectize.js', 'weight' => 300]);

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
}