<div class="panel clearfix">
    <div class="row g-0 clearfix section-header">
        <h4 class="col text-break">
            <?php echo $application->title; ?>

            <div class="float-end">
                <?php echo HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/form-application/show/$application->id", 'View', class: "btn btn-sm btn-primary") .
                    HtmlBootstrap5::box("/form-application/edit/$application->id", 'Edit', class: "btn btn-sm btn-secondary") .
                    HtmlBootstrap5::b("/form-application/export/$application->id", 'Export', class: "btn btn-sm btn-secondary") .
                    HtmlBootstrap5::b("/form-application/delete/$application->id", 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', class: "btn btn-sm btn-danger")
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

<div class="row">
    <div class="col-sm-12 col-md-6 pt-4">
        <div class="panel clearfix">
            <div class="row g-0 clearfix section-header">
                <h4 class="col">
                    Members

                    <?php echo HtmlBootstrap5::box("/form-application/edit_member/$application->id", 'Add Member', class: "btn btn-sm btn-primary float-end"); ?>
                </h4>
            </div>
            <div class="row">
                <?php if (empty($members)) : ?>
                    <p>No members found</p>
                <?php else : ?>
                    <?php echo $members; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 pt-4">
        <div class="panel clearfix">
            <div class="row g-0 clearfix section-header">
                <h4 class="col">
                    Attached Forms

                    <?php echo HtmlBootstrap5::box("/form-application/attach_form/$application->id", 'Attach Form', class: "btn btn-sm btn-primary float-end"); ?>
                </h4>
            </div>
            <div class="row">
                <?php if (empty($attached_forms)) : ?>
                    <p>No forms found</p>
                <?php else : ?>
                    <?php echo $attached_forms; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
