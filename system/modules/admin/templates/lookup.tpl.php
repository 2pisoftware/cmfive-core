<div class="tabs">
    <div class="tab-head">
        <a href="#tab-1">Lookup List</a>
        <a href="#tab-2">New Item</a>
    </div>
    <div class="tab-body">
        <div id="tab-1" class="clearfix">
            <?php echo Html::filter("Search Lookup Items", array(
                array("Type", "select", "type", $w->request("types"), $w->Admin->getLookupTypes())
            ), "/admin/lookup"); ?>
            <?php echo $listitem; ?>
        </div>
        <div id="tab-2" style="display: none;" class="clearfix">
            <?php echo $newitem; ?>
        </div>
    </div>
</div>
