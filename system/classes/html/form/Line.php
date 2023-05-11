<?php

namespace Html\Form;

/*
 *
 * Comments provided below and available parameters for this class are provided
 * by the Mozilla Developer Network
 * <https: //developer.mozilla.org/en/docs/Web/HTML/Element/textarea>
 *
 * @author Jareem Wheeler jareem@2pisoftware.com
 */
class Line extends \Html\Form\FormElement
{

    use \Html\GlobalAttributes, \Html\Events;

    public $form;
    public $name;
    public $required;
    public $value;

    static $_excludeFromOutput = ["value", "label"];

    /**
    * Returns built string of a Line
    *
    * @return string representation
    */
    public function __toString()
    {
        $buffer = '<hr>';

        return $buffer;
    }

    /**
     * The form element that the Line is associated with (its
     * "form owner"). The value of the attribute must be the ID of a form
     * element in the same document. If this attribute is not specified, the
     * <textarea> element must be a descendant of a form element. This attribute
     * enables you to place <textarea> elements anywhere within a document, not
     * just as descendants of their form elements.
     *
     * @param string $form
     * @return \Html\Form\Line
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * The name of the control.
     *
     * @param string $name
     * @return \Html\Form\Line
     */
    public function setname($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * This attribute is required by HtmlBootstrap5::multiColForm(...)
     *
     * @param string $required
     * @return \Html\Form\Line
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * The raw value contained in the control.
     *
     * @param string $value
     * @return \Html\Form\Line
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
