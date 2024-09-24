<div class="panel clearfix">
    <div class="row g-0 clearfix section-header">
        <h4 class="col">
            <?php echo $application->title; ?>
        </h4>
    </div>
    <?php if ($application->description != '') : ?>
        <div class="row">
            <p><?php echo $application->description; ?></p>
        </div>
    <?php endif; ?>
    <div class="row">
        <p><?php echo $application->description; ?></p>
    </div>
    <div class="row">
        <div class="col">
            <p>Active: <?php echo $application->is_active == 1 ? 'Yes' : 'No'; ?></p>
        </div>
        <div class="col">
            <?php echo HtmlBootstrap5::b('/form-application/edit/' . $application->id, 'Edit', null, null, false, "btn btn-primary float-end"); ?>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="small-12 medium-6 columns pt-4">
        <div class="panel clearfix">
            <div class="row g-0 clearfix section-header">
                <h4 class="col">
                    Members

                    <?php echo HtmlBootstrap5::box("/form-application/edit_member/$application->id", 'Add Member', true, class: "btn btn-sm btn-primary float-end"); ?>
                </h4>
            </div>
            <div class="row">
                <?php if(empty($members)) : ?>
                    <p>No members found</p>
                <?php else : ?>
                    <!--sdfkuhsdfjkdsjbkfhjksdjf-->
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="small-12 medium-6 columns pt-4">
        <div class="panel clearfix">
            <div class="row g-0 clearfix section-header">
                <h4 class="col">
                    Attached Forms

                    <?php echo HtmlBootstrap5::b('/form-application/attach_form/' . $application->id, 'Attach Form', null, null, false, "btn btn-sm btn-primary float-end"); ?>
                </h4>
            </div>
            <div class="row">
                <?php if(empty($members)) : ?>
                    <p>No forms found</p>
                <?php else : ?>
                    <!--sdfkuhsdfjkdsjbkfhjksdjf-->
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>