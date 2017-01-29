<p><em>If you have root access on <b><?= $db_host ?></b>
    you can create a new database from this CmFive installation and assign access to
    <b><?= $db_username ?></b>. All required tables
    will be automatically imported. <?= $more ?>
</em></p>
<label>Root Password
<input type="password" name="db_root" autocomplete="off" name='db_root'
    placeholder="Database creation requires root access" value="" /></label>
<label>New Database <?php if(!empty($app)) echo "<small><em>(Eg: " . $app . ")</small></em>"; ?>
<input type="text" name='<?= $name ?>' id='<?= $name ?>' required=true
    placeholder="Name of new database" value="" /></label>
