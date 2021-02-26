// import * as jQuery from 'jquery';

export class TabAdaptation {

    static changeTab(hash) {
        if (hash.length <= 0) {
            return
        }

        document.querySelectorAll(".tab-body > div").forEach((el: Element) => el.classList.remove('active'));
        document.querySelectorAll(".tab-head > a").forEach((el: Element) => el.classList.remove('active'));

        // jQuery(".tab-body > div").each(function() {
        //     jQuery(this).hide();
        //     jQuery(this).removeClass("active");
        // });
        // jQuery('.tab-head > a').each(function() {
        //     jQuery(this).removeClass("active")
        // })
        
        if (hash[0] === "#"){
            hash = hash.substr(1);
        }
        
        if (history.replaceState) {
            history.replaceState(null, null, '#' + hash);
        } else {
            location.hash = '#' + hash;
        }
        
        document.querySelector(".tab-body > div[id='" + hash + "']").classList.add('active');
        document.querySelector('.tab-head > a[href$="' + hash + '"]').classList.add('active');
        // jQuery(".tab-body > div[id='" + hash + "']").addClass("active");
        // jQuery('.tab-head > a[href$="' + hash + '"]').addClass("active");
        
        // Update codemirror instances
        // jQuery('.CodeMirror').each(function() {
        document.querySelectorAll('.CodeMirror').forEach((el: Element) => {
            if (el.hasOwnProperty('CodeMirror')) {
                (el as any).CodeMirror.refresh();
            }
        }); 
    }

    static bindTabInteractions() {
        // Backwards compat tabs
        document.querySelectorAll('.tab-head > a').forEach((el: Element) => {
        // jQuery(".tab-head").children("a").each(function() {
            if ((el as HTMLAnchorElement).href.indexOf("#") != -1) {
                const event = (event) => {
                    TabAdaptation.changeTab((el as HTMLAnchorElement).hash);
                    return false;
                }
                
                el.removeEventListener('click', event);
                el.addEventListener('click', event);

                // jQuery(this).on("click", {
                //     alink: (this as HTMLAnchorElement)
                // }, (event) => {
                //     TabAdaptation.changeTab(event.data.alink.hash);
                //     return false;
                // });
            }
        });

        var hash = window.location.hash.split("#")[1];
        if (hash && hash.length > 0) {
            TabAdaptation.changeTab(hash);
        } else {
            const target = document.querySelector(".tab-head > a:first");
            var me = document.createEvent('MouseEvents');
            me.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            target.dispatchEvent(me);
            // return true;
            // jQuery(".tab-head > a:first").trigger("click");
        }
    }
}