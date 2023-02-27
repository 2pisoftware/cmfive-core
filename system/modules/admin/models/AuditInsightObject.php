<?php

class AuditInsightObject extends InsightBaseClass
{
    public $name = "Audit Insight Object";
    public $description = "Drill through on audit information";

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array
    {

        return [
            "Options" => [
                [
                    [
                        "Object Class", "text", "class",  ""
                    ],
                    [
                        "Object Id", "text", "id", ""
                    ],
                ],
            ]
        ];
    }

    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array
    {
        // do as getObject without caching
        $data = AuditService::getInstance($w)->getObject(($parameters['class']), ($parameters['id']), false);

        if (!$data) {
            $results[] = new InsightReportInterface('Object Report', ['Results'], [['No data returned for selections']]);
        } else {
            // do as PDO select without any overrides (skip builder)
            $o = new $parameters['class']($w);
            $table = $o->getDbTableName();
            AuditService::getInstance($w)->_db->get($table)->where($o->getDbColumnName('id'), $parameters['id']);
            AuditService::getInstance($w)->_db->select(implode(",", $o->getDbTableColumnNames()));
            $result = AuditService::getInstance($w)->_db->fetchRow();
            // do as would apply to getObject if the select builder didn't exist
            $o->fill($result, false);
            // convert $data from list of objects to array of values
            $convertedData = [];
            foreach (get_object_vars($data) as $k => $v) {
                // skip any secret or nested properties!
                if ('_' !== substr($k, 0, 1) && 'w' !== $k) {
                    $row = [];
                    $row[] = $k;
                    // show as object, including typical UI reveal
                    $asUI = ('dt_' === substr($k ?? "", 0, 3)) ? (" :Formatted: " . formatDate($v) . " : " . formatDateTime($v)) : "";
                    $row[] = $v . $asUI;
                    // show as would be pre-cast by insert-or-update:
                    $row[] = $data->updateConvert($k, $v);
                    // show as object-without-builder, including typical UI reveal
                    $asUI = ('dt_' === substr($k ?? "", 0, 3)) ? (" :Formatted: " . formatDate($o->{$k}) . " : " . formatDateTime($o->{$k})) : "";
                    $row[] = (empty($o) ? "---" : $o->{$k} . $asUI);
                    // show as PDO select without any overrides (skip builder)
                    $row[] = (empty($result) ? "---" : $result[$k]);
                    $convertedData[] = $row;
                }
            }
            $results[] = new InsightReportInterface('Audit Report', ['All Properties', 'GetObject', 'WillPutAsUpdateConvert', 'FillWithoutBuildSelect', 'RawWithoutBuildSelect'], $convertedData);
        }
        return $results;
    }
}
