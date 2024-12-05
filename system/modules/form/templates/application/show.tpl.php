<style>
    p {
        margin-bottom: 0px;
    }

    #form-application__tab-body .active > .row-fluid {
        overflow-y: auto;
    }
</style>

<div class="panel clearfix">
    <div class="row g-0 clearfix section-header">
        <h4 class="col text-break">
            <?php echo $application->title; ?>

            <div class="float-end">
                <?php echo HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/form-application/manage/" . $application->id, 'Manage', class: "btn btn-sm btn-primary") .
                    HtmlBootstrap5::box("/form-application/edit/" . $application->id, 'Edit', class: "btn btn-sm btn-secondary") .
                    HtmlBootstrap5::b("/form-application/export/" . $application->id, 'Export', class: "btn btn-sm btn-secondary") .
                    HtmlBootstrap5::b("/form-application/delete/" . $application->id, 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', class: "btn btn-sm btn-danger")
                ); ?>
            </div>
        </h4>
    </div>
    <?php if ($application->description != '') : ?>
        <div class="row">
            <p class="text-break"><?php echo $application->description; ?></p>
        </div>
    <?php endif; ?>
    <div class="row">
        <p><?php echo $application->is_active ? 'Active' : 'Inactive'; ?></p>
    </div>
</div>

<div class="tabs pt-4">
    <div class="tab-head">
        <?php
        $tab_headers = $w->callHook('core_template', 'tab_headers', $application);

        if (!empty($tab_headers)) {
            echo implode('', $tab_headers);
        }
        ?>
    </div>
    <div class="tab-body" id="form-application__tab-body">
        <?php
        $tab_content = $w->callHook(
            'core_template',
            'tab_content',
            [
                'object' => $application,
                'redirect_url' => "/form-application/show/" . $application->id,
                'paginated' => true,
            ]
        );

        if (!empty($tab_content)) {
            echo implode('', $tab_content);
        }
        ?>
    </div>
</div>