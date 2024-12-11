<?php

function results_GET(Web $w)
{
    $response = ["success" => true, "data" => ""];
    $w->setLayout(null);
    $query = Request::string('q');
    $index = Request::string('idx');
    $page = Request::int('p');
    $page_size = Request::int('ps');
    $total_results = Request::int('tr');
    $tags = Request::mixed('tags');

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

                // filter results by permission to view
                foreach ($filter_results as $class => $objects) {
                    $filter_results[$class] = array_filter($objects, fn ($object) => $object->canList(AuthService::getInstance($w)->user()));
                }
                $filter_results = array_filter($filter_results, fn ($object) => !empty($object));

                foreach ($filter_results as $class => $objects) {
                    $title_added = false;

                    if (!empty($objects)) {
                        foreach ($objects as $object) {
                            $buffer .= "<div class='card mb-1'>";
                            $buffer .= '<div class="card-body">';
                            
                            // Only add the title once after we know at least one object can be listed.
                            if (!$title_added) {
                                $t_class = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $class);
                                $buffer .= "<h4 class='card-title'>{$t_class}</h4>";
                                $title_added = true;
                            }

                            if ($object->canView(AuthService::getInstance($w)->user())) {
                                $buffer .= "<a class='card-subtitle' href='{$w->localUrl($object->printSearchUrl())}'>";
                                $buffer .= $object->printSearchTitle();
                                $buffer .= "</a>";

                                $buffer .= "<div class='card-text'>{$object->printSearchListing()}</div>";
                            }
                            else {
                                $buffer .= "<div class='card-subtitle'>{$object->printSearchTitle()}</div>";
                                $buffer .= "<div class='card-text'>(restricted)</div>";
                            }

                            // if ($object->canView(AuthService::getInstance($w)->user())) {
                            //     $buffer .= "<a class='row search-title' href=" . $w->localUrl($object->printSearchUrl()) . ">{$object->printSearchTitle()}</a>"
                            //         . "<div class='row search-listing'>{$object->printSearchListing()}</div>";
                            // } else {
                            //     $buffer .= "<div class='small-12 columns search-title'>{$object->printSearchTitle()}</div><div class='row search-listing'>(restricted)</div>";
                            // }

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
