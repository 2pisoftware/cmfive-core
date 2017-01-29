<?php namespace Html;

/**
 * Trait for defining Global Events in HTML, nothing over DOM Level 3 will be
 * supported. Names and descriptions courtesy of 
 * w3schools.com <http://www.w3schools.com/jsref/dom_obj_event.asp>
 * 
 * Because using these attribtues in elements begin to blur the lines between
 * HTML and JavaScript, it is recommended that in Cmfive, for the sake of
 * maintainability, that you separate JavaScript event binding into <script> 
 * tags inside your template.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
trait Events {

	// Mouse events
	public $onclick;
	public $oncontextmenu;
	public $ondblclick;
	public $onmousedown;
	public $onmouseenter;
	public $onmouseleave;
	public $onmousemove;
	public $onmouseover;
	public $onmouseout;
	public $onmouseup;
	
	// Keyboard events
	public $onkeydown;
	public $onkeypress;
	public $onkeyup;
	
	// Frame/Object events
	public $onabort; // Also a media event
	public $onbeforeunload;
	public $onerror; // Also a media event
	public $onhashchange;
	public $onload;
	public $onpageshow;
	public $onpagehide;
	public $onresize;
	public $onscroll;
	public $onunload;
	
	// Form events
	public $onblur;
	public $onchange;
	public $onfocus;
	public $onfocusin;
	public $onfocusout;
	public $oninput;
	public $oninvalid;
	public $onreset;
	public $onsearch;
	public $onselect;
	public $onsubmit;
	
	// Drag events
	public $ondrag;
	public $ondragend;
	public $ondragenter;
	public $ondragleave;
	public $ondragover;
	public $ondragstart;
	public $ondrop;
	
	// Print events
	public $onafterprint;
	public $onbeforeprint;
	
	// Media events
	// $onabort is a Frame/Object event
	public $oncanplay;
	public $oncanplaythrough;
	public $ondurationchange;
	public $onemptied;
	public $onended;
	//  $onerror is a Frame/Object event
	public $onloadeddata;
	public $onloadedmetadata;
	public $onloadstart;
	public $onpause;
	public $onplay;
	public $onplaying;
	public $onprogress;
	public $onratechange;
	public $onseeked;
	public $onseeking;
	public $onstalled;
	public $onsuspend;
	public $ontimeupdate;
	public $onvolumechange;
	public $onwaiting;
	
	// Animation events
	public $animationend;
	public $animationiteration;
	public $animationstart;
	
	// Transition events
	public $transitionend;
	
	// Misc events
	public $onmessage;
	public $ononline;
	public $onoffline;
	public $onpopstate;
	public $onshow;
	public $onstorage;
	public $ontoggle;
	public $onwheel;

	/**
	 * The event occurs when the user clicks on an element.
	 * 
	 * @param string $onclick
	 * @return this
	 */
	public function setOnclick($onclick) {
		$this->onclick = $onclick;

		return $this;
	}

	/**
	 * The event occurs when the user right-clicks on an element to open a 
	 * context menu.
	 * 
	 * @param string $oncontextmenu
	 * @return this
	 */
	public function setOncontextmenu($oncontextmenu) {
		$this->oncontextmenu = $oncontextmenu;

		return $this;
	}

	/**
	 * The event occurs when the user double-clicks on an element.
	 * 
	 * @param string $ondblclick
	 * @return this
	 */
	public function setOndblclick($ondblclick) {
		$this->ondblclick = $ondblclick;

		return $this;
	}

	/**
	 * The event occurs when the user presses a mouse button over an element.
	 * 
	 * @param string $onmousedown
	 * @return this
	 */
	public function setOnmousedown($onmousedown) {
		$this->onmousedown = $onmousedown;

		return $this;
	}

	/**
	 * The event occurs when the pointer is moved onto an element.
	 * 
	 * @param string $onmouseenter
	 * @return this
	 */
	public function setOnmouseenter($onmouseenter) {
		$this->onmouseenter = $onmouseenter;

		return $this;
	}

	/**
	 * The event occurs when the pointer is moved out of an element.
	 * 
	 * @param string $onmouseleave
	 * @return this
	 */
	public function setOnmouseleave($onmouseleave) {
		$this->onmouseleave = $onmouseleave;

		return $this;
	}

	/**
	 * The event occurs when the pointer is moving while it is over an element.
	 * 
	 * @param string $onmousemove
	 * @return this
	 */
	public function setOnmousemove($onmousemove) {
		$this->onmousemove = $onmousemove;

		return $this;
	}

	/**
	 * The event occurs when the pointer is moved onto an element, or onto one
	 * of its children.
	 * 
	 * @param string $onmouseover
	 * @return this
	 */
	public function setOnmouseover($onmouseover) {
		$this->onmouseover = $onmouseover;

		return $this;
	}

	/**
	 * The event occurs when a user moves the mouse pointer out of an element, 
	 * or out of one of its children.
	 * 
	 * @param string $onmouseout
	 * @return this
	 */
	public function setOnmouseout($onmouseout) {
		$this->onmouseout = $onmouseout;

		return $this;
	}

	/**
	 * The event occurs when a user releases a mouse button over an element.
	 * 
	 * @param string $onmouseup
	 * @return this
	 */
	public function setOnmouseup($onmouseup) {
		$this->onmouseup = $onmouseup;

		return $this;
	}

	/**
	 * The event occurs when the user is pressing a key.
	 * 
	 * @param string $onkeydown
	 * @return this
	 */
	public function setOnkeydown($onkeydown) {
		$this->onkeydown = $onkeydown;

		return $this;
	}

	/**
	 * The event occurs when the user presses a key.
	 * 
	 * @param string $onkeypress
	 * @return this
	 */
	public function setOnkeypress($onkeypress) {
		$this->onkeypress = $onkeypress;

		return $this;
	}

	/**
	 * The event occurs when the user releases a key.
	 * 
	 * @param string $onkeyup
	 * @return this
	 */
	public function setOnkeyup($onkeyup) {
		$this->onkeyup = $onkeyup;

		return $this;
	}

	/**
	 * For Frame/Object events:
	 *		The event occurs when the loading of a resource has been aborted.
	 * For Media events:
	 *		The event occurs when the loading of a media is aborted.
	 * 
	 * @param string $onabort
	 * @return this
	 */
	public function setOnabort($onabort) {
		$this->onabort = $onabort;

		return $this;
	}

	/**
	 * The event occurs before the document is about to be unloaded.
	 * 
	 * @param string $onbeforeunload
	 * @return this
	 */
	public function setOnbeforeunload($onbeforeunload) {
		$this->onbeforeunload = $onbeforeunload;

		return $this;
	}

	/**
	 * For Frame/Object events:
	 *		The event occurs when an error occurs while loading an external file.
	 * For Media Events:
	 *		The event occurs when an error occurred during the loading of a 
	 *		media file
	 * 
	 * @param string $onerror
	 * @return this
	 */
	public function setOnerror($onerror) {
		$this->onerror = $onerror;

		return $this;
	}

	/**
	 * The event occurs when there has been changes to the anchor part of a URL.
	 * 
	 * @param string $onhashchange
	 * @return this
	 */
	public function setOnhashchange($onhashchange) {
		$this->onhashchange = $onhashchange;

		return $this;
	}

	/**
	 * The event occurs when an object has loaded.
	 * 
	 * @param string $onload
	 * @return this
	 */
	public function setOnload($onload) {
		$this->onload = $onload;

		return $this;
	}

	/**
	 * The event occurs when the user navigates to a webpage.
	 * 
	 * @param string $onpageshow
	 * @return this
	 */
	public function setOnpageshow($onpageshow) {
		$this->onpageshow = $onpageshow;

		return $this;
	}

	/**
	 * The event occurs when the user navigates away from a webpage.
	 * 
	 * @param string $onpagehide
	 * @return this
	 */
	public function setOnpagehide($onpagehide) {
		$this->onpagehide = $onpagehide;

		return $this;
	}

	/**
	 * The event occurs when the document view is resized.
	 * 
	 * @param string $onresize
	 * @return this
	 */
	public function setOnresize($onresize) {
		$this->onresize = $onresize;

		return $this;
	}

	/**
	 * The event occurs when an element's scrollbar is being scrolled.
	 * 
	 * @param string $onscroll
	 * @return this
	 */
	public function setOnscroll($onscroll) {
		$this->onscroll = $onscroll;

		return $this;
	}

	/**
	 * The event occurs once a page has unloaded (for <body>).
	 * 
	 * @param string $onunload
	 * @return this
	 */
	public function setOnunload($onunload) {
		$this->onunload = $onunload;

		return $this;
	}

	/**
	 * The event occurs when an element loses focus.
	 * 
	 * @param string $onblur
	 * @return this
	 */
	public function setOnblur($onblur) {
		$this->onblur = $onblur;

		return $this;
	}

	/**
	 * The event occurs when the content of a form element, the selection, or
	 * the checked state have changed (for <input>, <keygen>, <select>, and
	 * <textarea>).
	 * 
	 * @param string $onchange
	 * @return this
	 */
	public function setOnchange($onchange) {
		$this->onchange = $onchange;

		return $this;
	}

	/**
	 * The event occurs when an element gets focus.
	 * 
	 * @param string $onfocus
	 * @return this
	 */
	public function setOnfocus($onfocus) {
		$this->onfocus = $onfocus;

		return $this;
	}

	/**
	 * The event occurs when an element is about to get focus.
	 * 
	 * @param string $onfocusin
	 * @return this
	 */
	public function setOnfocusin($onfocusin) {
		$this->onfocusin = $onfocusin;

		return $this;
	}

	/**
	 * The event occurs when an element is about to lose focus.
	 * 
	 * @param string $onfocusout
	 * @return this
	 */
	public function setOnfocusout($onfocusout) {
		$this->onfocusout = $onfocusout;

		return $this;
	}

	/**
	 * The event occurs when an element gets user input.
	 * 
	 * @param string $oninput
	 * @return this
	 */
	public function setOninput($oninput) {
		$this->oninput = $oninput;

		return $this;
	}

	/**
	 * The event occurs when an element is invalid.
	 * 
	 * @param string $oninvalid
	 * @return this
	 */
	public function setOninvalid($oninvalid) {
		$this->oninvalid = $oninvalid;

		return $this;
	}

	/**
	 * The event occurs when a form is reset.
	 * 
	 * @param string $onreset
	 * @return this
	 */
	public function setOnreset($onreset) {
		$this->onreset = $onreset;

		return $this;
	}

	/**
	 * The event occurs when the user writes something in a search field (for 
	 * <input="search">).
	 * 
	 * @param string $onsearch
	 * @return this
	 */
	public function setOnsearch($onsearch) {
		$this->onsearch = $onsearch;

		return $this;
	}

	/**
	 * The event occurs after the user selects some text (for <input> and 
	 * <textarea>).
	 * 
	 * @param string $onselect
	 * @return this
	 */
	public function setOnselect($onselect) {
		$this->onselect = $onselect;

		return $this;
	}

	/**
	 * The event occurs when a form is submitted.
	 * 
	 * @param string $onsubmit
	 * @return this
	 */
	public function setOnsubmit($onsubmit) {
		$this->onsubmit = $onsubmit;

		return $this;
	}

	/**
	 * The event occurs when an element is being dragged.
	 * 
	 * @param string $ondrag
	 * @return this
	 */
	public function setOndrag($ondrag) {
		$this->ondrag = $ondrag;

		return $this;
	}

	/**
	 * The event occurs when the user has finished dragging an element.
	 * 
	 * @param string $ondragend
	 * @return this
	 */
	public function setOndragend($ondragend) {
		$this->ondragend = $ondragend;

		return $this;
	}

	/**
	 * The event occurs when the dragged element enters the drop target.
	 * 
	 * @param string $ondragenter
	 * @return this
	 */
	public function setOndragenter($ondragenter) {
		$this->ondragenter = $ondragenter;

		return $this;
	}

	/**
	 * The event occurs when the dragged element leaves the drop target.
	 * 
	 * @param string $ondragleave
	 * @return this
	 */
	public function setOndragleave($ondragleave) {
		$this->ondragleave = $ondragleave;

		return $this;
	}

	/**
	 * The event occurs when the dragged element is over the drop target.
	 * 
	 * @param string $ondragover
	 * @return this
	 */
	public function setOndragover($ondragover) {
		$this->ondragover = $ondragover;

		return $this;
	}

	/**
	 * The event occurs when the user starts to drag an element.
	 * 
	 * @param string $ondragstart
	 * @return this
	 */
	public function setOndragstart($ondragstart) {
		$this->ondragstart = $ondragstart;

		return $this;
	}

	/**
	 * The event occurs when the dragged element is dropped on the drop target.
	 * 
	 * @param string $ondrop
	 * @return this
	 */
	public function setOndrop($ondrop) {
		$this->ondrop = $ondrop;

		return $this;
	}

	/**
	 * The event occurs when a page has started printing, or if the print 
	 * dialogue box has been closed.
	 * 
	 * @param string $onafterprint
	 * @return this
	 */
	public function setOnafterprint($onafterprint) {
		$this->onafterprint = $onafterprint;

		return $this;
	}

	/**
	 * The event occurs when a page is about to be printed.
	 * 
	 * @param string $onbeforeprint
	 * @return this
	 */
	public function setOnbeforeprint($onbeforeprint) {
		$this->onbeforeprint = $onbeforeprint;

		return $this;
	}

	/**
	 * The event occurs when the browser can start playing the media (when it 
	 * has buffered enough to begin).
	 * 
	 * @param string $oncanplay
	 * @return this
	 */
	public function setOncanplay($oncanplay) {
		$this->oncanplay = $oncanplay;

		return $this;
	}

	/**
	 * The event occurs when the browser can play through the media without 
	 * stopping for buffering.
	 * 
	 * @param string $oncanplaythrough
	 * @return this
	 */
	public function setOncanplaythrough($oncanplaythrough) {
		$this->oncanplaythrough = $oncanplaythrough;

		return $this;
	}

	/**
	 * The event occurs when the duration of the media is changed.
	 * 
	 * @param string $ondurationchange
	 * @return this
	 */
	public function setOndurationchange($ondurationchange) {
		$this->ondurationchange = $ondurationchange;

		return $this;
	}

	/**
	 * The event occurs when something bad happens and the media file is 
	 * suddenly unavailable (like unexpectedly disconnects).
	 * 
	 * @param string $onemptied
	 * @return this
	 */
	public function setOnemptied($onemptied) {
		$this->onemptied = $onemptied;

		return $this;
	}

	/**
	 * The event occurs when the media has reach the end (useful for messages 
	 * like "thanks for listening").
	 * 
	 * @param string $onended
	 * @return this
	 */
	public function setOnended($onended) {
		$this->onended = $onended;

		return $this;
	}

	/**
	 * The event occurs when media data is loaded.
	 * 
	 * @param string $onloadeddata
	 * @return this
	 */
	public function setOnloadeddata($onloadeddata) {
		$this->onloadeddata = $onloadeddata;

		return $this;
	}

	/**
	 * The event occurs when meta data (like dimensions and duration) are loaded.
	 * 
	 * @param string $onloadedmetadata
	 * @return this
	 */
	public function setOnloadedmetadata($onloadedmetadata) {
		$this->onloadedmetadata = $onloadedmetadata;

		return $this;
	}

	/**
	 * The event occurs when the browser starts looking for the specified media.
	 * 
	 * @param string $onloadstart
	 * @return this
	 */
	public function setOnloadstart($onloadstart) {
		$this->onloadstart = $onloadstart;

		return $this;
	}

	/**
	 * The event occurs when the media is paused either by the user or 
	 * programmatically.
	 * 
	 * @param string $onpause
	 * @return this
	 */
	public function setOnpause($onpause) {
		$this->onpause = $onpause;

		return $this;
	}

	/**
	 * The event occurs when the media has been started or is no longer paused.
	 * 
	 * @param string $onplay
	 * @return this
	 */
	public function setOnplay($onplay) {
		$this->onplay = $onplay;

		return $this;
	}

	/**
	 * The event occurs when the media is playing after having been paused or
	 * stopped for buffering.
	 * 
	 * @param string $onplaying
	 * @return this
	 */
	public function setOnplaying($onplaying) {
		$this->onplaying = $onplaying;

		return $this;
	}

	/**
	 * The event occurs when the browser is in the process of getting the media
	 * data (downloading the media).
	 * 
	 * @param string $onprogress
	 * @return this
	 */
	public function setOnprogress($onprogress) {
		$this->onprogress = $onprogress;

		return $this;
	}

	/**
	 * The event occurs when the playing speed of the media is changed.
	 * 
	 * @param string $onratechange
	 * @return this
	 */
	public function setOnratechange($onratechange) {
		$this->onratechange = $onratechange;

		return $this;
	}

	/**
	 * The event occurs when the user is finished moving/skipping to a new 
	 * position in the media.
	 * 
	 * @param string $onseeked
	 * @return this
	 */
	public function setOnseeked($onseeked) {
		$this->onseeked = $onseeked;

		return $this;
	}

	/**
	 * The event occurs when the user starts moving/skipping to a new position 
	 * in the media.
	 * 
	 * @param string $onseeking
	 * @return this
	 */
	public function setOnseeking($onseeking) {
		$this->onseeking = $onseeking;

		return $this;
	}

	/**
	 * The event occurs when the browser is trying to get media data, but data 
	 * is not available.
	 * 
	 * @param string $onstalled
	 * @return this
	 */
	public function setOnstalled($onstalled) {
		$this->onstalled = $onstalled;

		return $this;
	}

	/**
	 * The event occurs when the browser is intentionally not getting media data.
	 * 
	 * @param string $onsuspend
	 * @return this
	 */
	public function setOnsuspend($onsuspend) {
		$this->onsuspend = $onsuspend;

		return $this;
	}

	/**
	 * The event occurs when the playing position has changed (like when the
	 * user fast forwards to a different point in the media).
	 * 
	 * @param string $ontimeupdate
	 * @return this
	 */
	public function setOntimeupdate($ontimeupdate) {
		$this->ontimeupdate = $ontimeupdate;

		return $this;
	}

	/**
	 * The event occurs when the volume of the media has changed (includes
	 * setting the volume to "mute").
	 * 
	 * @param string $onvolumechange
	 * @return this
	 */
	public function setOnvolumechange($onvolumechange) {
		$this->onvolumechange = $onvolumechange;

		return $this;
	}

	/**
	 * The event occurs when the media has paused but is expected to resume
	 * (like when the media pauses to buffer more data).
	 * 
	 * @param string $onwaiting
	 * @return this
	 */
	public function setOnwaiting($onwaiting) {
		$this->onwaiting = $onwaiting;

		return $this;
	}

	/**
	 * The event occurs when a CSS animation has completed.
	 * 
	 * @param string $animationend
	 * @return this
	 */
	public function setAnimationend($animationend) {
		$this->animationend = $animationend;

		return $this;
	}

	/**
	 * The event occurs when a CSS animation is repeated.
	 * 
	 * @param string $animationiteration
	 * @return this
	 */
	public function setAnimationiteration($animationiteration) {
		$this->animationiteration = $animationiteration;

		return $this;
	}

	/**
	 * The event occurs when a CSS animation has started.
	 * 
	 * @param string $animationstart
	 * @return this
	 */
	public function setAnimationstart($animationstart) {
		$this->animationstart = $animationstart;

		return $this;
	}

	/**
	 * The event occurs when a CSS transition has completed.
	 * 
	 * @param string $transitionend
	 * @return this
	 */
	public function setTransitionend($transitionend) {
		$this->transitionend = $transitionend;

		return $this;
	}

	/**
	 * The event occurs when a message is received through or from an object
	 * (WebSocket, Web Worker, Event Source or a child frame or a parent window).
	 * 
	 * @param string $onmessage
	 * @return this
	 */
	public function setOnmessage($onmessage) {
		$this->onmessage = $onmessage;

		return $this;
	}

	/**
	 * The event occurs when the browser starts to work online.
	 * 
	 * @param string $ononline
	 * @return this
	 */
	public function setOnonline($ononline) {
		$this->ononline = $ononline;

		return $this;
	}

	/**
	 * The event occurs when the browser starts to work offline.
	 * 
	 * @param string $onoffline
	 * @return this
	 */
	public function setOnoffline($onoffline) {
		$this->onoffline = $onoffline;

		return $this;
	}

	/**
	 * The event occurs when the window's history changes.
	 * 
	 * @param string $onpopstate
	 * @return this
	 */
	public function setOnpopstate($onpopstate) {
		$this->onpopstate = $onpopstate;

		return $this;
	}

	/**
	 * The event occurs when a <menu> element is shown as a context menu.
	 * 
	 * @param string $onshow
	 * @return this
	 */
	public function setOnshow($onshow) {
		$this->onshow = $onshow;

		return $this;
	}

	/**
	 * The event occurs when a Web Storage area is updated.
	 * 
	 * @param string $onstorage
	 * @return this
	 */
	public function setOnstorage($onstorage) {
		$this->onstorage = $onstorage;

		return $this;
	}

	/**
	 * The event occurs when the user opens or closes the <details> element.
	 * 
	 * @param string $ontoggle
	 * @return this
	 */
	public function setOntoggle($ontoggle) {
		$this->ontoggle = $ontoggle;

		return $this;
	}

	/**
	 * The event occurs when the mouse wheel rolls up or down over an element.
	 * 
	 * @param string $onwheel
	 * @return this
	 */
	public function setOnwheel($onwheel) {
		$this->onwheel = $onwheel;

		return $this;
	}

}
