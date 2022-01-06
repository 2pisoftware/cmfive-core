<?php
echo $template_select;

$button = new \Html\button();
$button->type("submit")->text("Export")->setClass('tiny button savebutton escape-reveal-modal');
$button->form("template_select_form");
echo $button;
