<?php namespace Html;

class FormBootstrap5 extends form
{
    public function close($button_title = 'Save', $extrabuttons = null) {
        if (!empty($button_title)) {
            $button = new \Html\button();
            $button->_class = 'btn';
            $button->type("submit")->text($button_title)->setClass('btn-primary savebutton');
            $cancel_button = new \Html\button();
            $cancel_button->_class = 'btn';
            $cancel_button->setClass('btn-secondary form-cancel-button');
            $cancel_button->type("button")->text("Cancel");

            return "<div class='row form-button-container'><div class='col'>{$button->__toString()}{$cancel_button->__toString()}{$extrabuttons}</div></div></form>";
        } else {
            return "</form>";
        }
    }
}
