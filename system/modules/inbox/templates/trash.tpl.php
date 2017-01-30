<?php
if (!empty($del_table)) {
    $button = new \Html\button();
    echo $button->text(__("Delete Forever"))->onclick("deleteMessage()")->__toString(); // print "<button onclick='deleteMessage()'>Delete</button>";

    echo $del_table;
} else {
    echo "<br/>".__("No deleted messages.");
}

$last_page = ceil($del_count / 40);
$minPage = ($pgnum * 1) - 5;
($minPage <= 0) ? $minPage = 1 : '';
//print $minPage . "\n";
$maxPage = ($pgnum * 1) + 5;
($maxPage > $last_page) ? $maxPage = $last_page : '';
//print $maxPage . "\n";
//exit();

if ($last_page > 1) {
    print "<table style='margin:auto;'><tr id='nav'>";
    if ($pgnum > 1) {
        print "<td style='background-color:#eee;' id='link" . $i . " prevlink' class='link' onclick='switchPage(" . ($pgnum - 1) . ")'><a class='link'  href='#'>".__('Prev')."</a></td>&nbsp";
    }
    for ($i = $minPage; $i <= $maxPage; $i++) {
        if ($pgnum == $i) {
            print "<td id='link" . $i . " ' class='link ispage' ><b>*" . $i . "*</b></td>&nbsp";
        } else {
            print "<td id='link" . $i . "' class='link' onclick='switchPage(" . $i . ")'><a class='link'  href='#'>" . $i . "</a></td>&nbsp";
        }
    }
    if ($pgnum < $last_page && $last_page !== 1) {
        print "<td style='background-color: #eee; width:30px;' id='link" . $i . " nextlink' class='link' onclick='switchPage(" . ($pgnum + 1) . ")'><a class='link'  href='#'>".__("Next")."</a></td>&nbsp";
    }
    print "</tr></table>";
}
?>
<script type="text/javascript">

    $(".ispage").css("cursor", "default");
    $(".ispage").hover(function() {
        $(this).css("background-color", "#CAFF70")
    });
    $(document).ready(function() {
        for (var i = 1; i <<?php echo $last_page; ?> + 1; i++) {
            if (i == 1) {
                $("#link" + i).addClass('selectedPage');
            } else {
                $("#link" + i).removeClass('selectedPage');
            }
        }
    });

    function switchPage(page) {
        window.location.href = "<?php echo $webroot; ?>/inbox/trash/" + page;
    }

    function selectAll() {
        $(":checkbox").attr("checked", "checked");
    }
    function deleteMessage() {
        var check = new Array();
        var count = 0;
        $(":checkbox:checked").each(function() {
            check[count] = $(this).val();
            count++;
        });
        if (count !== 0) {
            window.location.href = "<?php echo $webroot; ?>/inbox/deleteforever/" + check;
        }
    }
</script>
