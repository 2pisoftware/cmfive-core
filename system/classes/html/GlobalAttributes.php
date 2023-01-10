<?php namespace Html;

/**
 * Trait implmentation for HTML attribtues that are consistent across all elements.
 * Names and descriptions are courtesy of the Mozilla Developer Network
 * <https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes>
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
trait GlobalAttributes
{

    public $_attributeList = [];
    public $accesskey;
    public $class;
    public $contenteditable;
    public $contextmenu;

    // The data attribtues are dynamically added since they can be anything

    public $dir;
    public $draggable;
    public $dropzone;
    public $hidden;
    public $id;

    // The following 5 attributes are part of the WHATWG HTML Microdata feature
    public $itemid;
    public $itemprop;
    public $itemref;
    public $itemscope;
    public $itemtype;

    public $lang;
    public $spellcheck;
    public $style;
    public $tabindex;
    public $title;
    public $translate;

    // Non standard Cmfive setters

    /**
     * Sets any attribute by adding it as a parameter to the class using this
     * trait.
     *
     * WARNING: may cause issues where there are naming collisions. This
     * function is best used to include non-standard attributes such as the ones
     * used by Angular JS.
     *
     * @param string $name
     * @param Mixed $value
     * @return this
     */
    public function setAttribute($name, $value)
    {
        // @todo: does this cause issues with names that aren't valid php?
        $this->{$name} = $value;
        array_push($this->_attributeList, [$name => $value]);

        return $this;
    }

    // HTML5 setters

    /**
     * Provides a hint for generating a keyboard shortcut for the current
     * element. This attribute consists of a space-separated list of characters.
     * The browser should use the first one that exists on the computer
     * keyboard layout.
     *
     * @param string $accesskey
     * @return this
     */
    public function setAccesskey($accesskey)
    {
        $this->accesskey = $accesskey;

        return $this;
    }

    /**
     * Is a space-separated list of the classes of the element. Classes allows
     * CSS and JavaScript to select and access specific elements via the class
     * selectors or functions like the method Document.getElementsByClassName().
     *
     * @param string $class
     * @return this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Is an enumerated attribute indicating if the element should be editable
     * by the user. If so, the browser modifies its widget to allow editing. The
     * attribute must take one of the following values:
     *     - true or the empty string, which indicates that the element must be
     *            editable;
     *     - false, which indicates that the element must not be editable.
     *
     * @param string $contenteditable
     * @return this
     */
    public function setContenteditable($contenteditable)
    {
        $this->contenteditable = $contenteditable;

        return $this;
    }

    /**
     * WARNING: Firefox support only!
     * Is the id of a <menu> to use as the contextual menu for this element.
     *
     * @param string $contextmenu
     * @return this
     */
    public function setContextmenu($contextmenu)
    {
        $this->contextmenu = $contextmenu;

        return $this;
    }

    /**
     * Forms a class of attributes, called custom data attributes, that allow
     * proprietary information to be exchanged between the HTML and its DOM
     * representation that may be used by scripts. All such custom data are
     * available via the HTMLElement interface of the element the attribute is
     * set on. The HTMLElement.dataset property gives access to them.
     *
     * The given key is appended to the string "data-" and then used as the
     * actual key for the given value.
     *
     * i.e. if you want the attribute 'data-meaning-of-life="42"' to be printed
     * in your element, then call
     *        ->setData("meaning-of-life", "42")
     *
     * @param string $key
     * @param string $value
     * @return this
     */
    public function setData($key, $value)
    {
        $this->{"data-" . $key} = $value;

        return $this;
    }

    /**
     * Is an enumerated attribute indicating the directionality of the
     * element's text. It can have the following values: ltr, rtl, auto
     *
     * @param string $dir
     * @return this
     */
    public function setDir($dir)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * Is an enumerated attribute indicating whether the element can be dragged,
     * using the Drag and Drop API. It can have the following values:
     *        true, false
     *
     * @param string $draggable
     * @return this
     */
    public function setDraggable($draggable)
    {
        $this->draggable = $draggable;

        return $this;
    }

    /**
     * Is an enumerated attribute indicating what types of content can be
     * dropped on an element, using the Drag and Drop API. It can have the
     * following values:
     *        copy, which indicates that dropping will create a copy of the
     *            element that was dragged.
     *        move, which indicates that the element that was dragged will be
     *            moved to this new location.
     *        link, will create a link to the dragged data.
     *
     * @param string $dropzone
     * @return this
     */
    public function setDropzone($dropzone)
    {
        $this->dropzone = $dropzone;

        return $this;
    }

    /**
     * Is a Boolean attribute indicates that the element is not yet, or is no
     * longer, relevant. For example, it can be used to hide elements of the
     * page that can't be used until the login process has been completed. The
     * browser won't render such elements. This attribute must not be used to
     * hide content that could legitimately be shown.
     *
     * @param string $hidden
     * @return this
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Defines a unique identifier (ID) which must be unique in the whole
     * document. Its purpose is to identify the element when linking (using a
     * fragment identifier), scripting, or styling (with CSS).
     *
     * @param string $id
     * @return this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * The unique, global identifier of an item.
     * See <https://html.spec.whatwg.org/multipage/microdata.html#microdata> for
     * more information.
     *
     * @param string $itemid
     * @return this
     */
    public function setItemid($itemid)
    {
        $this->itemid = $itemid;

        return $this;
    }

    /**
     * Used to add properties to an item. Every HTML element may have an
     * itemprop attribute specified, where an itemprop consists of a name and
     * value pair.
     *
     * See <https://html.spec.whatwg.org/multipage/microdata.html#microdata> for
     * more information.
     *
     * @param string $itemprop
     * @return this
     */
    public function setItemprop($itemprop)
    {
        $this->itemprop = $itemprop;

        return $this;
    }

    /**
     * Properties that are not descendants of an element with the itemscope
     * attribute can be associated with the item using an itemref. Itemref
     * provides a list of element ids (not itemids) with additional properties
     * elsewhere in the document.
     *
     * See <https://html.spec.whatwg.org/multipage/microdata.html#microdata> for
     * more information.
     *
     * @param string $itemref
     * @return this
     */
    public function setItemref($itemref)
    {
        $this->itemref = $itemref;

        return $this;
    }

    /**
     * Itemscope (usually) works along with itemtype to specify that the HTML
     * contained in a block is about a particular item. itemscope creates the
     * Item and defines the scope of the itemtype associated with it. itemtype
     * is a valid URL of a vocabulary (such as schema.org) that describes the
     * item and its properties context.
     *
     * See <https://html.spec.whatwg.org/multipage/microdata.html#microdata> for
     * more information.
     *
     * @param string $itemscope
     * @return this
     */
    public function setItemscope($itemscope)
    {
        $this->itemscope = $itemscope;

        return $this;
    }

    /**
     * Specifies the URL of the vocabulary that will be used to define
     * itemprop's (item properties) in the data structure. Itemscope is used to
     * set the scope of  where in the data structure the vocabulary set by
     * itemtype will be active.
     *
     * See <https://html.spec.whatwg.org/multipage/microdata.html#microdata> for
     * more information.
     *
     * @param string $itemtype
     * @return this
     */
    public function setItemtype($itemtype)
    {
        $this->itemtype = $itemtype;

        return $this;
    }

    /**
     * Participates in defining the language of the element, the language that
     * non-editable elements are written in or the language that editable
     * elements should be written in. The tag contains one single entry value in
     * the format defines in the Tags for Identifying Languages (BCP47) IETF
     * document. xml:lang has priority over it.
     *
     * @param string $lang
     * @return this
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Is an enumerated attribute defines whether the element may be checked for
     * spelling errors. It may have the following values: true, false
     *
     * @param string $spellcheck
     * @return this
     */
    public function setSpellcheck($spellcheck)
    {
        $this->spellcheck = $spellcheck;

        return $this;
    }

    /**
     * Contains CSS styling declarations to be applied to the element. Note that
     * it is recommended for styles to be defined in a separate file or files.
     * This attribute and the <style> element have mainly the purpose of
     * allowing for quick styling, for example for testing purposes.
     *
     * @param string $style
     * @return this
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Is an integer attribute indicates if the element can take input focus
     * (is focusable), if it should participate to sequential keyboard
     * navigation, and if so, at what position. It can takes several values:
     *        a negative value means that the element should be focusable, but
     *            should not be reachable via sequential keyboard navigation;
     *        0 means that the element should be focusable and reachable via
     *            sequential keyboard navigation, but its relative order is
     *            defined by the platform convention;
     *        a positive value which means should be focusable and reachable via
     *            sequential keyboard navigation; its relative order is defined by
     *            the value of the attribute: the sequential follow the increasing
     *            number of the tabindex. If several elements share the same
     *            tabindex, their relative order follows their relative position
     *            in the document).
     *
     * @param type $tabindex
     * @return type
     */
    public function setTabindex($tabindex)
    {
        $this->tabindex = $tabindex;

        return $this;
    }

    /**
     * Contains a text representing advisory information related to the element
     * it belongs to. Such information can typically, but not necessarily, be
     * presented to the user as a tooltip.
     *
     * @param string $title
     * @return this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Is an enumerated attribute that is used to specify whether an element's
     * attribute values and the values of its Text node children are to be
     * translated when the page is localized, or whether to leave them
     * unchanged. It can have the following values:
     *        empty string and "yes", which indicates that the element will be
     *            translated.
     *        "no", which indicates that the element will not be translated.
     *
     * @param type $translate
     * @return type
     */
    public function setTranslate($translate)
    {
        $this->translate = $translate;

        return $this;
    }
}
