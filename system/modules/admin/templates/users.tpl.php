<div class="mb-3">
    
    <?php 
    echo HtmlBootstrap5::box($webroot . "/admin/useradd/box", "Add New User", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary'); 
    echo HtmlBootstrap5::filter("Filter Users", $filterData, "/admin/users", "GET", "Filter", "users_filter");
    ?>
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