<div class='tabs'>
    <div class='tab-head'>
        <a class='active' href="#tab-1">Lookup List</a>
        <a href="#tab-2">New Item</a>
    </div>
    <div class='tab-body'>
        <div id='tab-1'>
            <?php echo HtmlBootstrap5::filter("Search Lookup Items", array(
                array("Type", "select", "type", $selectedtype, LookupService::getInstance($w)->getLookupTypes(), "form-select")
            ), "/admin/lookup"); ?>
            <?php echo $listitem; ?>
        </div>
        <div id='tab-2'>
            <?php echo $newitem; ?>
        </div>
    </div>
</div>