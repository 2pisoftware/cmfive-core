// src/js/Events.ts

/**
 * OK, look, this is messy as all hell and hard to maintain
 * I needed something quick and easy and this works...
 *
 * The reason why this is here is because javascript is a pain in the rear.
 * In third party cmfive modules, if you dynamically add content and want to use
 * core cmfive functionality (like modals) then you need some way of binding
 * the interactions of Cmfive's JS core since the page has already loaded and
 * new content wont automatically inherit these features.
 * 
 * BUT, if you try and import the base Cmfive ts and call the bind function,
 * you'll notice that things stop working. This is because the code for Cmfive
 * (and bootstrap et al.) now runs TWICE, since you imported all initial bindings 
 * as well in your 3rd party module code.
 * 
 * So how do you get around this? With a damn event bus that was registered in
 * Cmfive core js and tied to the global window object.
 * 
 * Thank you for listening to my ted talk.
 * 
 * I'm ashamed to do this, but:
 * @author Adam Buckley <adam@2pisoftware.com>
 */
export class Events {
    static dispatchDomUpdateEvent(target: Element) {
        // @ts-ignore
        const eventBusElement = window.cmfiveEventBus;
        if (eventBusElement) {
            eventBusElement.dispatchEvent(
                new CustomEvent('dom-update', { 
                    detail: target
                })
            )
        }
    }
}
