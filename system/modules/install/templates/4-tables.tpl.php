<fieldset>
    <legend>Select database</legend>
<?php if(empty($_SESSION['install']['saved']['db_username'])) : ?>
    <p>You have not yet configured the database connection.</p>
    <?php echo $w->partial('skip', array('skip' => $step-1, 'button' => 'Configure', 'clazz' =>'primary'), 'install');
else:
    if(isset($databases) && count($databases) > 0): ?>
            <div id='available_databases'>
                <table>
                    <tbody>
                        <?php
                            $db_database = $_SESSION['install']['saved']['db_database'];
                            $total = count($databases);
                            foreach($databases as $database => $tables): ?>
                        <tr>
                            <td>
                                <div class="switch tiny round">
                                    <input id="database_<?= $database; ?>" type="radio" value="<?= $database; ?>" name="db_database"
                                        <?php if(strcmp($database, $db_database) === 0 || $total === 1) echo " checked"; ?>
                                        class="<?= ($tables['total'] == 0 ? 'empty_database' :
                                             (!isset($tables['migration']) ? 'no_migrations' : 'cmfive_database')) ?>"/>
                                    <label for="database_<?= $database; ?>"></label>
                                </div>
                            </td>
                            <td>
                                <label for="database_<?= $database; ?>"><?= $database; ?>
                                    <?php if($tables['total'] == 0): ?>
                                        <span class="label round success">This database is empty</span>
                                    <?php elseif(!isset($tables['migration'])): ?>
                                        <span class="label round alert"><?= $tables['total'] ?> tables, no migrations</span>
                                    <?php else: ?>
                                        <span class="label round alert">
                                            <?= $tables['total'] ?> tables,
                                            <?= count($tables['migration']) ?> migrations,
                                                last migration: <?= date('g:ia jS F Y',
                                                strtotime($tables['migration'][count($tables['migration'])-1]['dt_created'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </label>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td style='vertical-align: top;'>
                                <div class="switch tiny round">
                                    <input id="database_new" type="radio" value="" name="db_database"
                                        <?php if($total === 0) echo " checked"; ?> />
                                    <label for="database_new"></label>
                                </div>
                            </td>
                            <td>
                                <label for="database_new"><b>Or... Create new database</b></label>
                                <div id='create_database' style='display:none;'>
                                    <fieldset>
                                        <?php echo $w->partial('tables_form', array('name' => 'db_database'), 'install'); ?>
                                    </fieldset>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <fieldset id='show_import_tables' style='display:none'>
                <legend>Import tables</legend>
                <p><i>Warning: Importing required tables will overwrite the selected database with a fresh installation</i></p>
                <div class="row switch-row">
                    <div class="column">
                        <div class="switch tiny round">
                            <input type="hidden" id="database_import_hidden" name="database_import" value="false" />
                            <input type="checkbox" id="database_import" value="true" />
                            <label for="database_import"></label><!-- use to make background blue //-->
                        </div>
                    </div>
                    <div class="column">
                        <label for="database_import" id="database_import_label">Import database tables</label>
                    </div>
                </div>
            </fieldset>
            <br/>
            <button class="button" type="button" id="select_database">Next</button>
    <?php else: ?>
            <legend>Create New Database</legend>
            <?php echo $w->partial('tables_form', array('more' => '<b>CmFive requires a database to run.</b>',
                                                        'name' => 'db_database'), 'install'); ?>
            <button class="button" type="button" id="select_database">Next</button>
    <?php endif; ?>
<?php endif; ?>
<button class="button secondary" type="button" id="refresh_button">Refresh</button>
<?php echo $w->partial('skip', array('skip' => $step+1), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
                       
    $('#refresh_button').click(refresh_databases);
    $('#select_database').click(select_database);
    $('input[name=db_database]').change(toggle_create);
    toggle_create();
                       
    $('#database_import').change(handle_import_change);
    handle_import_change();
                       
    function toggle_create()
    {
        // stop weirdness
        if(!$('#create_database').length) return;
                       
        //console.log('create new database');
        var showCreate = $('#database_new:checked').length > 0;
                       
        //$('#database_new').val(showCreate);
        $('#import_tables').attr('disabled', showCreate);
        $('#create_database input[type=text]').attr('disabled', !showCreate);

        // grey everything out using a css class
        if(showCreate)
        {
            $('#show_import_tables').slideUp();
            $('#create_database').slideDown();
            //$('#database_label').removeClass('gray'); // american spelling
        }
        else
        {
            $('#create_database').slideUp();
             //$('#database_label').addClass('gray'); // american spelling
                       
            if($('input[name=db_database]:checked').hasClass('empty_database'))
                $('#show_import_tables').slideUp();
            else
                $('#show_import_tables').slideDown();
            
        }
    }
       
    function handle_import_change()
    {
        $('#database_import_hidden').val($('#database_import:checked').length);

        // grey everything out using a css class
        if($('#database_import:checked').length)
        {
            $('#database_import_label').removeClass('disabled');
        }
        else
        {
            $('#database_import_label').addClass('disabled');
        }
    }

    function select_database()
    {
                       
        //console.log(($('#database:checked').length ? 'create_database' : 'select_database') +
        //               ", db=" + $("input[name=db_database]:checked").val());

        // set the value of the radio button
        if($('#db_database').length && $('#database_new').length)
        {
            $('#database_new').val($('#db_database').val());
        }

        if(!$('#database_new').length || $('#database_new:checked').length)
        {
            CmFiveAjax.performAjax('database'); // create a new database
        }
        else if($('input[name=db_database]:checked').hasClass('empty_database') ||
                $('#database_import_hidden').val())
        {
            CmFiveAjax.performAjax('import'); // import tables
        }
        else
        {
             $('form').submit(); // just select it
        }
    }

                       
    function refresh_databases()
    {
        window.location = "/install/<?= $step ?>/tables";
    }
});


// AJAX after
function ajax_database(result)
{
    if(result['success'] > 0)
    {
        $('form').submit();
        //console.log('ajax_database');
        //console.log(result);
    }
}

// AJAX after
function ajax_import(result)
{
    if(result['success'] > 0)
    {
        $('form').submit();
    }
}

//-->
</script>
