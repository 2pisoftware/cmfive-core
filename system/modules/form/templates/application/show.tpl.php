<style>
    p {
        margin-bottom: 0px;
    }

    #form-application-<?php echo $application->id; ?>__tab-body .active > .row-fluid {
        overflow-y: auto;
    }
</style>
<div class='row panel'>
    <div class='col-12 col-md-9'></div>
        <p>Title: <?php echo $application->title; ?></p>
        <p>Description: <?php echo $application->description; ?></p>
        <p>Active: <?php echo $application->is_active == 1 ? 'Yes' : 'No'; ?></p>
    </div>
    <div class='col-12 col-md-3'>
        <div class='row'>
            <div class='col-6'>
                <?php echo HtmlBootstrap5::b('/form-application/edit/' . $application->id, 'Edit', null, null, false, "btn btn-primary btn-block"); ?> </br>
                <?php echo HtmlBootstrap5::b('/form-application/export/'.$application->id, 'Export', null, null, false, "btn btn-primary btn-block"); ?>
            </div>
            <div class='col-6'>
                <?php echo HtmlBootstrap5::b('/form-application/delete/' . $application->id, 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', null, false, "btn btn-danger btn-block"); ?>
            </div>
        </div>
    </div>
</div>
<div class="tabs">
    <div class="tab-head">
        <?php
        $tab_headers = $w->callHook('core_template', 'tab_headers', $application);
        if (!empty($tab_headers)) {
            echo implode('', $tab_headers);
        }
        ?>
    </div>
    <div class="tab-body" id="form-application-<?php echo $application->id; ?>__tab-body">
        <?php
        $tab_content = $w->callHook('core_template', 'tab_content', ['object' => $application, 'redirect_url' => '/form-application/show/' . $application->id]);
        if (!empty($tab_content)) {
            echo implode('', $tab_content);
        }
        ?>
    </div>
</div>