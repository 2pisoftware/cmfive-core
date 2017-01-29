<?php
    $step = $w->Install->findInstallStep('timezone');
    
    echo '"' . $_SESSION['install']['saved']['timezone'] . '" GMT ';
    $gmt = $_SESSION['install']['saved']['gmt'];
    if($gmt > 0) echo "+" . $gmt;
        else echo $gmt;
?>