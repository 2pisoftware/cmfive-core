<?php
// export a recordset as CSV
public function exportcsv($rows, $title)
{
    // set filename
    $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".csv";

    // if we have records, comma delimit the fields/columns and carriage return delimit the rows
    if (!empty($rows)) {
        foreach ($rows as $row) {
            //throw away the first line which list the form parameters
            $crumbs = array_shift($row);
            $title = array_shift($row);
            $hds = array_shift($row);
            $hvals = array_values($hds);
            
            // find key of any links
            foreach ($hvals as $h) {
                if (stripos($h, "_link")) {
                    list($fld, $lnk) = preg_split("/_/", $h);
                    $ukey[] = array_search($h, $hvals);
                    unset($hds[$h]);
                }
            }

            // iterate row to build URL. if required
            if (!empty($ukey)) {
                foreach ($row as $r) {
                    foreach ($ukey as $n => $u) {
                        // dump the URL related fields for display
                        unset($r[$u]);
                    }
                    $arr[] = $r;
                }
                $row = $arr;
                unset($arr);
            }

            $csv = new ParseCsv\Csv();
            $csv->output_filename = $filename;
            // ignore lib wrapper csv->output, to keep control over header re-sends!
            $this->w->out($csv->unparse($row, $hds, null, null, null));
            // can't use this way without commenting out header section, which composer won't like
            // $this->w->out($csv->output($filename, $row, $hds));
            unset($ukey);
        } 
        $this->w->sendHeader("Content-type", "application/csv");
        $this->w->sendHeader("Content-Disposition", "attachment; filename=" . $filename);
        $this->w->setLayout(null); 
    }
}


$insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
$run_data = $insight->run($w, $_GET);

//Clicking the button will trigger this action.
$filename = $insight_name . date('Y.m.d-H.i') . ".csv";
//$run_data doesn't include the parameters in its dataset
if(!empty($run_data)) {
    $run_data = array_values($run_data);
}
