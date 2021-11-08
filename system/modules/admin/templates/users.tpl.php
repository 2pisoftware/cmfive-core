<div class="mb-3">
    <?php echo Html::box($webroot . "/admin/useradd/box", "Add New User", true); ?>
</div>
<div class='tabs'>
    <div class='tab-head'>
        <a class='active' href='#internal'>Internal</a>
        <a href='#external'>External</a>
    </div>
    <div class='tab-body'>
        <div id='internal'>
            <?php echo $internal_table; ?>
        </div>
        <div id='external'>
            <?php echo $external_table; ?>
        </div>
    </div>
</div>