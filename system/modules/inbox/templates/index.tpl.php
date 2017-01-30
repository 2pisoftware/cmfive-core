<?php
if ($w->Auth->user()->allowed("/inbox/send")) {
    echo Html::b($webroot."/inbox/send",__("Create Message"));
}
$button = new \Html\button();
if (!empty($new)) {
    echo $button->text(__("Archive"))->onclick("sendArch()")->__toString(); // print "<button onclick='sendArch()'>Archive</button>";
    echo $button->text(__("Delete"))->onclick("deleteMessage()")->__toString(); // print "<button onclick='deleteMessage()'>Delete</button>";
}

if($w->service('Inbox')->inboxCountMarker()){
    echo Html::b($w->localUrl("/inbox/allread"),__("Mark all read"),__("Are you sure to mark all messages as read?"));
}

if (!empty($new)) {
    // Print table
    echo $new_table; 

    $last_page = ceil($newtotal/40);
    $minPage = ($pgnum*1)-5;
    ($minPage <= 0) ? $minPage = 1 : '';

    $maxPage = ($pgnum*1)+5;
    ($maxPage > $last_page) ? $maxPage = $last_page : '';

    if ($last_page > 1){
        print "<table style='margin:auto;'><tr id='nav'>";
        if($pgnum > 1){
            print "<td style='background-color:#eee;' id='link".$i." prevlink' class='link' onclick='switchPage(".($pgnum-1).")'><a class='link'  href='#'>".__('Prev')."</a></td>&nbsp";
        }
        for($i=$minPage;$i<=$maxPage;$i++){
            if ($pgnum == $i){
                print "<td id='link".$i." ' class='link ispage' ><b>*".$i."*</b></td>&nbsp";
            } else {
                print "<td id='link".$i."' class='link' onclick='switchPage(".$i.")'><a class='link'  href='#'>".$i."</a></td>&nbsp";
            }
        }
        if ($pgnum < $last_page && $last_page !== 1){
            print "<td style='background-color: #eee; width:30px;' id='link".$i." nextlink' class='link' onclick='switchPage(".($pgnum+1).")'><a class='link'  href='#'>".__('Next')."</a></td>&nbsp";
        }
        print "</tr></table>";
    }
} else {
    $url = $webroot."/inbox/read";
    print "<br/><br/>".__("No new messages").", <a href='".$url."'>".__("click here")."</a> ".__("to go to your read messages.");
}

?>

<script type='text/javascript'>
        var all_select = false;
        
	$(".ispage").css("cursor","default");
	$(".ispage").hover(function(){$(this).css("background-color","#CAFF70")});
	$(document).ready(function(){
            for(var i = 1; i < <?php echo !empty($pgcount) ? $pgcount : 1; ?>+1; i++){
                if (i == 1){
                    $("#link"+i).addClass('selectedPage');
                } else {
                    $("#link"+i).removeClass('selectedPage');
                }
            }
	});

	function switchPage(page){
            window.location.href = "<?php echo $webroot; ?>/inbox/index/"+page;
	}
        
	function selectAll(){
            
            $(".classChk:checkbox").each(function(event) {
                // If all select false then we want to tick everything
                if (all_select == false) {
                    if (!$(this).attr("checked") || $(this).attr("checked") == "") { 
                        $(this).attr("checked", "checked");
                    }
                    all_select == true;
                } else {
                    // Else untick everything
                    $(this).removeAttr("checked");
                    all_select = false;
                }
            });
	}

	function sendArch(){
            var check = new Array();
            var count = 0;
            $(".classChk:checkbox:checked").each(function(){
                check[count] = $(this).val();
                count++;
            });
            if (count !== 0){
                window.location.href = "<?php echo $webroot; ?>/inbox/archive/index/"+check;
            }
	}

	function deleteMessage(){
            var check = new Array();
            var count = 0;
            $(".classChk:checkbox:checked").each(function(){
                check[count] = $(this).val();
                count++;
            });
            if (count !== 0){
                window.location.href = "<?php echo $webroot; ?>/inbox/delete/index/"+check;
            }
	}
</script>
