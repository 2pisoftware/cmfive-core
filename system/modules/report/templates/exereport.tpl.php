<?php echo (!empty($exportxml) ? $exportxml : "") .
    (!empty($exportcsv) ? $exportcsv : "") .
    (!empty($exportpdf) ? $exportpdf : "") .
    (!empty($btnrun) ? $btnrun : "") .
    (!empty($btnview) ? $btnview : ""); ?>
<br /><br />

<div style="overflow-y: auto">
    <?php echo $showreport; ?>
</div>