<div class="tabs">
    <div class="tab-head">
        <?php if (!empty($insight_class)) : ?>
            <a href="#templates">Templates</a>
            <a href="#members">Members</a>
        <?php endif;?>
        <a href="#database">View Database</a>
    </div>
    <div class="tab-body">
    <div id="templates">
                <p>You can add special templates to render the data. Create a <a href="#">System Template in Admin</a> and set the
                module to <b>report</b>, it can then be selected here.</p>
                <p>The template processer uses the Twig language, you can find more information about this on
                the <a href="#">Twig Website</a>.</p>
                <p>A good first step when creating a new template, is to look at the data. You can use the following
                twig statement in your template to do this:</p>
                <pre>{{dump(data)}}</pre>
                <p></p>
                <?php echo Html::box("/insight-templates/edit/{$insight_class}", "Add Template", true); ?>
                <?php echo !empty($insight_templates_table) ? $insight_templates_table : ""; ?>
            </div>
            <div id="members" style="display: none;" class="clearfix">
                <?php echo Html::box("/report/addmembers/" . $report->id, " Add New Members ", true) ?>
                <?php echo $viewmembers; ?>
            </div>
        <?php endif;?>