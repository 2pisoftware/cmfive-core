<?php namespace Html\Form;

/**
 * Cmfive FormElement class, an extension of the Element class which defines the
 * addition "label" parameter which is used by the form builder to add the label
 * of a form field to the page.
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class FormElement extends \Html\Element
{

    public $label;

    /**
     * Sets the printable label used by {@see Html::multiColForm()}
     *
     * @param String $label
     * @return \Html\Form\FormElement
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
