<?php 
	echo Html::b("/tag/admin", "Back to Tag list", false);
	echo !empty($edittagform) ? $edittagform : '';