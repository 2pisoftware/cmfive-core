<?php echo (!empty($exportxml) ? $exportxml : "") . 
           (!empty($exportcsv) ? $exportcsv : "") . 
           (!empty($exportpdf) ? $exportpdf : "") . 
           (!empty($btnrun) ? $btnrun : "") . 
           (!empty($btnview) ? $btnview : ""); ?>
<br/><br/>
<?php echo $showreport; ?>
