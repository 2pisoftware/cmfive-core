<?php
/**
 * template for favorites partial
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 **/
$tabHead=array();
$buffer='';	
if (!empty($categorisedFavorites)) {
	foreach($categorisedFavorites as $className => $objects) {
		// Transform class into readable text
		$t_class = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $className);
		$tabHead[$className]='<a href="#'.$className.'">'.str_replace(' ','&nbsp;',$t_class).'</a>';
		if (!empty($objects)) {
			$buffer.='<div id="'.$className.'" >';
			$buffer .= "<div class='row search-class'><h4 style='padding-left: 30px; font-weight: lighter;'>{$t_class}</h4>";
		
			foreach($objects as $templateData) {
				//if ($templateData->canList(AuthService::getInstance($w)->user())) {
					$buffer .= '<div class="panel search-result">';
					//if ($templateData->canView(AuthService::getInstance($w)->user())) {
					$buffer .= "<a class=\"row search-title\" href=\"{$w->localUrl($templateData['url'])}\">{$templateData['title']}</a>"
					. "<div class=\"row search-listing\">{$templateData['listing']}</div>";
					$buffer .= "</div>";
				//}
			}
		}
		$buffer .= "</div>";	
		$buffer .= "</div>";
	}
}
?>
<div class="tab-head">
        <?php  echo implode("",$tabHead); ?>
    </div>
    <div class="tab-body">
        <?php  
        echo $buffer; ?>
	</div>
</div>
