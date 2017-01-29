jQuery(document).ready(function($) {
    jQuery(".fade-in").each(function(index, element) {
        $(this).delay(index * 300).fadeIn(500);
    });

    CmFiveAjax.init();
});


var CmFiveAjax = {

    init: function() {
        this.WP_ERROR   = 'alert';
        this.WP_WARNING = 'warning';
        this.WP_INFO    = 'success';

        this.form = jQuery('form#install_form');
        this.ajaxurl = '/install/ajax/';
        
        this.debug = false; // override php ajax result debug

        jQuery(".fade-in").each(function(index, element) {
            jQuery(this).delay(index * 300).fadeIn(500);
        });

        jQuery('body').on('click', '.cmfive-ajax-click', function(e){
            var func = jQuery(e.target).attr('func');
            if(func && func.length)
                CmFiveAjax.performAjax(func);
        });

        jQuery('body').on('change', '.cmfive-ajax-change', function(e){
            var func = jQuery(e.target).attr('func');
            if(func && func.length)
                CmFiveAjax.performAjax(func);
        });

        jQuery('body').on('keypress', '.cmfive-ajax-enter', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                var func = jQuery(e.target).attr('func');
                if(func && func.length)
                    CmFiveAjax.performAjax(func);
            }
        });

        jQuery('body').on('click', '.show_debug_link', CmFiveAjax.showDebugInfo);

        //jQuery('body').on('click', '.notice', CmFiveAjax.hideAlert);
    },

    // notice-error will display the message with a white background and a red left border.
    showError: function(error)
    {
        CmFiveAjax.showAlert(error, null, null, CmFiveAjax.WP_ERROR);
    },

    showAjaxError: function(error, response, result)
    {
        CmFiveAjax.showAlert(error, response, result, CmFiveAjax.WP_ERROR);
    },

    //Use notice-warning for a yellow/orange
    showWarning: function(warning)
    {
        CmFiveAjax.showAlert(warning, null, null, CmFiveAjax.WP_WARNING);
    },

    showAjaxWarning: function(warning, response, result)
    {
        CmFiveAjax.showAlert(warning, response, result, CmFiveAjax.WP_WARNING);
    },

    //notice-info for a blue left border.
    showInfo: function(info)
    {
        CmFiveAjax.showAlert(info, null, null, CmFiveAjax.WP_INFO);
    },

    showAjaxInfo: function(info, response, result)
    {
        CmFiveAjax.showAlert(info, response, result, CmFiveAjax.WP_INFO);
    },

    // https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
    showAlert: function(msg, response, result, type)
    {
        if(msg == null || msg.length == 0)
            msg = "Oh no!"; // default error message

        var formatted_json = '';
        var obj = '';
        var stacktrace = '';

        // format the returned ajax result or response, if there is one.
        if(result != null && typeof result == 'object' && (result.debug || CmFiveAjax.debug))
        {
            console.log(result);
            formatted_json = CmFiveAjax.formatJSON(JSON.stringify(result));
        }
        else if(response != null && typeof(response) == 'string' && response.length && CmFiveAjax.debug)
        {
            console.log(response);
            formatted_json = CmFiveAjax.formatJSON(response);
        }

        // if an Error Object was passed in as a message, we need to handle that
        if(typeof msg == 'string')
        {
            msg.trim();
            if(msg.indexOf('<') !== 0)
                msg = "<p>" + msg + "</p>";
        }
        else if(typeof msg == 'object')
        {
            console.log(msg.constructor);
            if(msg instanceof Error)
            {
                stacktrace = CmFiveAjax.getStackTrace(msg);
                msg = '<p>' + msg.name + ': ' + msg.message + '</p>';
            }
            else if(result != null && typeof result == 'object' && (result.debug || CmFiveAjax.debug))
            {
                obj = CmFiveAjax.print_r(msg);
                msg = '<p>Object: ' + obj.constructor.name + '</p>';
            }
            else if(msg.message)
            {
                msg = msg.message;
            }
            else // no debug, keep it simple... but informative
            {
                msg = obj.constructor.name;
            }
        }

        // output the message to the console, without debugging info
        console.log(msg);

        // if an admin notice is already present, we need to make that go away
        jQuery('.notice').slideUp();

        // debug
        if(formatted_json.length || obj.length || stacktrace.length)
        {
            if(!stacktrace.length)
                stacktrace = CmFiveAjax.getStackTrace();

            msg += "<div class='show_debug_link'><p>Show Debug Information</p></div>\n" +
                    "<div class='show_debug_info' style='display:none'>\n";

            if(obj.length)
                msg += "<h4>Object</h4>\n<pre>" + obj + "</pre>\n";

            if(formatted_json.length)
                msg += "<h4>Formatted JSON</h4>\n<pre>" + formatted_json + "</pre>\n";

            msg += "<h4>Stack Trace</h4>\n<pre>" + CmFiveAjax.print_r(stacktrace, '') + "</pre>\n</div>";
        }

        // find the admin notice
        var alertBox = jQuery("div.alert-box." + type);

        if(alertBox.length) // alert box exists, just change the html inside
        {
            alertBox.html(msg);

            if(msg.length != 0)
                alertBox.slideDown();
        }
        else if(msg.length) // prepend the alert box
        {
            jQuery('.install_breadcrumbs').before("<div data-alert class='alert-box " + type + "'>" + msg + "</div>");
            alertBox = jQuery("div.alert-box." + type);
        }

        // make it scroll in an animated pretty way to the top of the page
        if(alertBox.length)
        {
            // add an x to the top of the box, to make it go away
            alertBox.addClass('is-dismissible');

            jQuery('html, body').animate({
                scrollTop: 0
            }, 1000);
        }

        // can make it disappear after a number of seconds
        //alertBox.delay(8000).slideUp();
    },

    showDebugInfo: function(e)
    {
        var link = jQuery(e.target).closest('.show_debug_link');
        var info = link.next('.show_debug_info');
        if(info.length)
        {
            info.slideToggle();
            link.toggleClass('show_debug_open');
        }
    },

    handleAjaxReponse: function(response) {

        if(response === 0)
        {
            CmFiveAjax.showError("Ajax was not run successfully");
            return;
        }

        var result;
        try
        {
            result = JSON.parse(response);
        }
        catch(e)
        {
            CmFiveAjax.showAjaxError(e, response, null);
        }

        if(result)
        {
            if(result.error)
            {
                CmFiveAjax.showAjaxError(result.error, response, result);
                return;
            }
            else if(result.warning)
            {
                CmFiveAjax.showAjaxWarning(result.warning, response, result);
                return;
            }
            else if(!result.success)
            {
                CmFiveAjax.showAjaxError("No error message... but still unsuccessful.", response, result);
                return;
            }

            // redirect to a url
            if(result.url)
            {
                window.location = result.url;
            }

            if(result.debug)
            {
                console.log(result);
            }

            // show a success message if there is one
            if(result.successMsg)
            {
                CmFiveAjax.showAjaxInfo(result.successMsg, response, result);
            }

            // swap out the html
            if(result.html && result.div)
            {
                jQuery(result.div).html(result.html);
            }

            // pass it onto another function
            if(result.functionName)
            {
                try
                {
                    var fn = window["ajax_" + result.functionName];
                    if(typeof fn === 'function') {
                        fn(result);
                    }
                }
                catch(e)
                {
                    console.log(e);
                }
            }
        }
    },

    formatJSON: function(json) {
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/([\}\]],?)/g, "</div>$1</div>").replace(/([\{\[])/g, "<div>$1<div>").replace(/,\"/g, ",</div><div>\"");
    },

    performAjax: function(func, data) {
        if(!data || typeof data == 'undefined')
        {
            data = CmFiveAjax.form.serializeArray();
        }

        data.push({ name: "action", value: func });

        jQuery.ajax({
            url: CmFiveAjax.ajaxurl + func,
            data: data,
            type: 'POST',
            cache: false,
            success: CmFiveAjax.handleAjaxReponse,
            error: CmFiveAjax.handleAjaxReponse
        });
    },

    getStackTraceFirefox: function(e)
    {
        var regex = /\s+at ([^(]+) \(.+?([A-Za-z0-9\-_\.]+\.[A-Za-z0-9_]+)\?.*:([0-9]+)\)/g;
        var callstack = [];
        var m;
        while((m = regex.exec(e.stack)) != null)
        {
            if (m.index === regex.lastIndex)
            regex.lastIndex++;

            callstack.push("\t" + m[1] + "()\n\t" + m[2] + "\n\tLine " + m[3] + "\n");
        }

        //Remove call to printStackTrace()
        callstack.shift();
        return callstack;
    },

    getStackTraceOpera: function(e)
    {
        var callstack = [];
        var lines = e.message.split("\n");
        for (var i=0, len=lines.length; i<len; i++)
        {
            if (lines[i].match(/^\s*[A-Za-z0-9\-_\$]+\(/))
            {
                var entry = lines[i];
                //Append next line also since it has the file info
                if (lines[i+1])
                {
                    entry += ' at ' + lines[i+1];
                    i++;
                }
                callstack.push(entry);
            }
        }
                
        //Remove call to printStackTrace()
        callstack.shift();
        return callstack;
    },

    getStackTraceIE: function() // or chrome
    {
        var currentFunction = arguments.callee.caller;
        while (currentFunction)
        {
            var fn = currentFunction.toString();
            var fname = fn.substring(fn.indexOf("function") + 8, fn.indexOf('(')).trim() + "()";
            if(fname.length == 0)
                fname = 'anonymous';                
            if(callstack)
                callstack.push(fname);
            currentFunction = currentFunction.caller;
        }
    },

    generateError: function()
    {
        try
        {
            notExistantVariable += 10; //throw a garunteed error
        }
        catch(e)
        {
            return e;
        }
    },

    getStackTrace: function(e)
    {
        var callstack = [];

        if(e == null || typeof e != 'object' || !(e instanceof Error))
        {
            e = CmFiveAjax.generateError();
        }

        if(e.stack)  //Firefox
            callstack = CmFiveAjax.getStackTraceFirefox(e);

        else if(window.opera && e.message) //Opera
            callstack = CmFiveAjax.getStackTraceOpera(e);

        if(callstack.length == 0) //IE and Safari
            CmFiveAjax.getStackTraceIE();

        return callstack;
    },


    print_r: function(theObj, oldStr)
    {
        if(!theObj) return oldStr;
        var str = oldStr;

        if(theObj.constructor == Array || theObj.constructor == Object)
        {
            str += "<ul>\n";
            for(var p in theObj)
            {
                if(theObj[p].constructor == Array || theObj[p].constructor == Object)
                {
                    str += "<li>["+p+"] => "+typeof(theObj)+"</li>\n";
                    str += "<ul>\n";
                    str += CmFiveAjax.print_r(theObj[p], str);
                    str += "</ul>\n";
                }
                else
                {
                    str += "<li>["+p+"] => "+theObj[p]+"</li>\n";
                }
            }
            str += "</ul>\n";
        }
        else if(theObj.constructor == String)
            str += "\"" + theObj + "\"";
        else
            str += theObj;

        return str;
    }

}


