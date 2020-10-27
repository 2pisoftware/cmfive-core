<?php

function results_GET(Web $w)
{
    $response = ["success" => true, "data" => ""];
    $w->setLayout(null);
    $query = $w->request('q');
    $index = $w->request('idx');
    $page = $w->request('p');
    $page_size = $w->request('ps');
    $total_results = $w->request('tr');
    $tags = $w->request('tags');

    if (($query && strlen($query) >= 3) || (!empty($tags))) {
        if (!empty($tags)) {
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $query .= ' unitag' . strtolower(preg_replace('%[^a-z]%i', '', $tag));
                }
            } else {
                $query .= ' unitag' . strtolower(preg_replace('%[^a-z]%i', '', $tags));
            }
        }

        $results = SearchService::getInstance($w)->getResults($query, $index, $page, $page_size);

        if (empty($page) && empty($page_size) && empty($total_results)) {
            $buffer = "";
            if (!empty($results[0])) {
                $filter_results = [];

                // Group results by class_name.
                foreach ($results[0] as $res) {
                    $searchobject = SearchService::getInstance($w)->getObject($res['class_name'], $res['object_id']);
                    if (!empty($searchobject)) {
                        $filter_results[$res['class_name']][] = $searchobject;
                    }
                }

                foreach ($filter_results as $class => $objects) {
                    $title_added = false;

                    if (!empty($objects)) {
                        foreach ($objects as $object) {
                            if (!$object->canList(AuthService::getInstance($w)->user())) {
                                continue;
                            }

                            // Only add the title once after we know at least one object can be listed.
                            if (!$title_added) {
                                $t_class = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $class);
                                $buffer .= "<div class='row search-class'><h4 style='padding-top: 10px; font-weight: lighter;'>{$t_class}</h4>";
                                $title_added = true;
                            }

                            $buffer .= '<div class="panel search-result">';

                            if ($object->canView(AuthService::getInstance($w)->user())) {
                                $buffer .= "<a class=row search-title href=" . $w->localUrl($object->printSearchUrl()) . ">{$object->printSearchTitle()}</a>"
                                    . "<div class=row search-listing>{$object->printSearchListing()}</div>";
                            } else {
                                $buffer .= "<div class=small-12 columns search-title>{$object->printSearchTitle()}</div><div class=row search-listing>(restricted)</div>";
                            }

                            $buffer .= "</div>";
                        }
                    }

                    $buffer .= "</div>";
                }
            }

            $response["data"] = $buffer;
        }
    } else {
        $response["success"] = false;
        $response["data"] = "Please enter at least 3 characters for searching.";
    }

    $w->out(json_encode($response));
}
