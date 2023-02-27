<?php

class AuditInsightObject extends InsightBaseClass
{
    public $name = "Audit Insight Object";
    public $description = "Drill through on audit information";

    //Displays Filters to select user
    public function getFilters(Web $w, $parameters = []): array
    {
        // $moduleSelectOptions = AuditService::getInstance($w)->getLoggedModules();
        // $actionSelectOptions = AuditService::getInstance($w)->getLoggedActions();

        return [
            "Options" => [
                [
                    [
                        "Class", "input", "class",  null
                    ],
                    [
                        "Id", "input", "id", null
                    ],
                ],
            ]
        ];
    }

    //Displays insights for selections made in the above "Options"
    public function run(Web $w, $parameters = []): array
    {
        //below service is referred to as $where in subsequent notes in this block for purpose of examples
        $data = AuditService::getInstance($w)->getObject(($parameters['class']), ($parameters['id']),false);

$cached =   $obj = !empty(self::$_cache[$parameters['class']][$parameters['id']]) ? self::$_cache[$parameters['class']][$parameters['id']] : null;

$o = new $parameters['class']($w);
        $table = $o->getDbTableName();
            AuditService::getInstance($w)->_db->get($table)->where($o->getDbColumnName('id'), $parameters['id']);

//        AuditService::getInstance($w)->buildSelect($o, $table, $parameters['class']);
    AuditService::getInstance($w)->_db->select(implode(",",$o->getDbTableColumnNames()));
      

      $result = AuditService::getInstance($w)->_db->fetchRow();
 $o->fill($result, false);


        if (!$data) {
             $results[] = new InsightReportInterface('Object Report', ['Results'], [['No data returned for selections']]);
        } else {
            // convert $data from list of objects to array of values
            $convertedData = [];
            foreach (get_object_vars($data) as $k => $v) {
            
            if ('_' !== substr($k, 0, 1) && 'w' !== $k) {
$asUI = ('dt_' === substr($k ?? "",0,3))?(" :Formatted: ".formatDate($v)." : ".formatDateTime($v)):"";
                $row = [];
                $row[] = $k;
                $row[] = $v . $asUI;
                $row[] = $data->updateConvert($k, $v);
$row[] = (empty($cached)?"---":$cached->{$k});

$asUI = ('dt_' === substr($k ?? "",0,3))?(" :Formatted: ".formatDate($o->{$k})." : ".formatDateTime($o->{$k})):"";
$row[] = (empty($o)?"---":$o->{$k}.$asUI);
$row[] = (empty($result)?"---":$result[$k]);
                $convertedData[] = $row;
}
            }
            $results[] = new InsightReportInterface('Audit Report', ['All Properties','GetObject','PutAsUpdateConvert','Cached','FillAfterBypassedBuildSelect','RawFromBypassedBuildSelect'], $convertedData);
        }
        return $results;
    }
}
