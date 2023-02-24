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
        $data = AuditService::getInstance($w)->getObject(($parameters['class']), ($parameters['id']));

$cached =   $obj = !empty(self::$_cache[$parameters['class']][$parameters['id']]) ? self::$_cache[$parameters['class']][$parameters['id']] : null;

$o = new $parameters['class']($w);
        $table = $o->getDbTableName();
            AuditService::getInstance($w)->_db->get($table)->where($o->getDbColumnName('id'), $parameters['id']);

//        AuditService::getInstance($w)->buildSelect($o, $table, $parameters['class']);
    AuditService::getInstance($w)->_db->select(implode(",",$o->getDbTableColumnNames()));
      

      $result = AuditService::getInstance($w)->_db->fetchRow();
 $o->fill($result, true);


        if (!$data) {
             $results[] = new InsightReportInterface('Object Report', ['Results'], [['No data returned for selections']]);
        } else {
            // convert $data from list of objects to array of values
            $convertedData = [];
            foreach (get_object_vars($data) as $k => $v) {
            
            if ('_' !== substr($k, 0, 1) && 'w' !== $k) {
                $row = [];
                $row[] = $k;
                $row[] = $v;
                $row[] = $data->updateConvert($k, $v);
$row[] = (empty($cached)?"---":$cached->{$k});
$row[] = (empty($o)?"---":$o->{$k});
$row[] = (empty($result)?"---":$result[$k]);
                $convertedData[] = $row;
}
            }
            $results[] = new InsightReportInterface('Audit Report', ['All Properties','GetObject','PutAsReadConvert','Cached','FillBypassedBuildSelect','RawBypassedBuildSelect'], $convertedData);
        }
        return $results;
    }
}
