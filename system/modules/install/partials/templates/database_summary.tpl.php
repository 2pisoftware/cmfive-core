<?php
    $installStep = $w->Install->getInstallStep('database');
    
    echo $_SESSION['install']['saved']['db_driver'] . " " .
         $installStep->getTestResultStr('check_connection', "CONNECTED", "FAILED", "UNTESTED");
?>
<ul>
    <li><b>Host:</b> <?= '"' . $_SESSION['install']['saved']['db_host'] . ':' . $_SESSION['install']['saved']['db_port'] . '"' ?></li>
    <?php if(!empty($_SESSION['install']['saved']['db_username'])) : ?>
        <li><b>User:</b> <?= '"' . $_SESSION['install']['saved']['db_username'] . '" ' .
        (empty($_SESSION['install']['saved']['db_password']) ? "NO" : "USING") . ' PASSWORD'; ?></li>
    <?php endif; ?>
</ul>
