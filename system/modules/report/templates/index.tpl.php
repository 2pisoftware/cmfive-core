<?php //echo Html::filter("Search Reports", array(array("Modules", "select", "module", !empty($reqModule) ? $reqModule : null, $modules)), "/report/index", "POST", "Search Reports", "leadFilter", null); ?>
<?php echo $viewreports; ?>

<script>
    var resetFlag = true;
    var module_url = "/report/reportAjaxListModules";

    $(document).ready(function() {
        $.getJSON(module_url + $(this).val(), function(result) {
            $('#module').parent().html(result);
            $("select#module").val("<?php echo !empty($reqModule) ? $reqModule : ""; ?>");
            $("select[id='module']").trigger("change");
        });
    });

    $.ajaxSetup ({
        cache: false
    });

    $("#clrForm").click(function(e) {
        e.preventDefault();
        resetFlag = false;
        $("select#module").val("");
        $("select[id='module']").trigger("change");
    });

    var cat_url = "/report/reportAjaxModuletoCategory?id="; 
    $("select[id='module']").on("change", function() {
        $.getJSON(cat_url + $(this).val(), function(result) {
            $('#category').parent().html(result);
            if (resetFlag) {
                $("select#category").val("<?php echo !empty($reqCategory) ? $reqCategory : ""; ?>");
            }
            $("select[id='category']").trigger("change");
        });
    });

    var type_url = "/report/reportAjaxCategorytoType?id="; 
    $("select[id='category']").on("change", function() {
        $.getJSON(type_url + $(this).val() + "_" + $("select[id='module']").val(), function(result) {
            $('#type').parent().html(result);
            if (resetFlag)
                $("select#type").val("<?php echo !empty($reqType) ? $reqType : ""; ?>");
            }
        );
    });
</script>
