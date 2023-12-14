<?php
function view_GET(Web &$w)
{
    $p = $w->pathMatch("m", "a");
    // first see if we need to split into sub modules
    $module = $p['m'];
    $action = $p['a'];

    // check if help is allowed for this topic
    if (!AuthService::getInstance($w)->allowed($p['m'] . '/' . $p['a'])) {
        $w->ctx("help_content", "Sorry, there is no help for this topic.");
    }

    $submodule = "";
    // check for submodule
    if (strcontains($p['m'], array("-"))) {
        $ms = explode("-", $p['m']);
        $module = $ms[0];
        $submodule = $ms[1];
    }

    // find a module toc
    $tocf = getHelpFileContent($w, $module, null, $module . "_toc");
    if ($tocf) {
        $w->ctx("module_toc", $module . '/' . $module . "_toc");
        $w->ctx("module_title", HelpLib::extractTitle($tocf));
    }

    // load help file
    $help_file = HelpLib::getHelpFilePath($w, $module, $submodule, $action);
    $content = "Sorry, this help topic is not yet written.";
    if (!empty($help_file) && file_exists($help_file)) {
        $content = file_get_contents($help_file);
    }


    // set context
    $w->ctx("help_content", helpMarkup(pruneRestricted($w, $content), $module));
    $w->ctx("module", $module);
    $w->ctx("submodule", $submodule);
    $w->ctx("action", $action);
}

/**
 * Remove restricted paragraphs from help file
 * So that only parts are left which the user is
 * allowed to see
 *
 * restricted parts are marked as:
 *
 * [[restricted|role1,role2,role3...]]
 * restricted text paragraph
 * [[endrestricted]]
 *
 * @param Web $w
 * @param string $content
 */
function pruneRestricted($w, $content)
{
    $c = "";
    $restricted = false;
    foreach (explode("\r\n", $content) as $l) {
        if (preg_match_all("/\[\[restricted\|(.*?)\]\]/", $l, $matches)) {
            $roles = explode(',', $matches[1][0]);
            if (!AuthService::getInstance($w)->user()->hasAnyRole($roles)) {
                $restricted = true;
            }
        } else if (startsWith($l, "[[endrestricted]]")) {
            $restricted = false;
        } else if (!$restricted) {
            $c .= $l . "\r\n";
        }
    }
    return $c;
}

function replaceImage($content, $module)
{
    $img = '<img src="' . WEBROOT . '/help/media/' . $module . '/\\1" border="0"/>';
    return preg_replace("/\[\[img\|(.*?)\]\]/", $img, $content);
}

function getHelpFileContent(Web &$w, $module, $submodule, $action)
{
    $p = HelpLib::getHelpFilePath($w, $module, $submodule, $action);
    if ($p) {
        return file_get_contents($p);
    }
    return null;
}


function replaceVideo($content, $module)
{
    $video = "<span style=\" -moz-border-radius: 6px;
                                     -moz-box-shadow: 0 0 14px #123;
                                     display: -moz-inline-stack;
                                     display: inline-block;
                                    border: 2px solid black;\">";
    $video .= '<a href="' . WEBROOT . '/help/media/' . $module . '/\\2" style="display:block;width:700px;height:394px;" id="video\\1"></a>';
    $video .= '<script language="JavaScript">flowplayer("video\\1", "' . WEBROOT . '/js/flowplayer/flowplayer-3.2.5.swf", {clip: {autoPlay:false, autoBuffering:true, scaling:"fit"}});</script>';
    $video .= "</span>";
    return preg_replace("/\[\[video\|(.*?)\|(.*?)\]\]/", $video, $content);
}
function helpMarkup($content, $module)
{
    $content = str_replace("\r\n\r\n", "<p>", $content);
    $content = preg_replace("/\[\[title\|(.*?)\]\]/", "<h2>\\1</h2>", $content);
    $content = preg_replace("/\[\[button\|(.*?)\]\]/", "<button>\\1</button>", $content);
    $content = preg_replace("/\[\[help\|(.*?)\|(.*?)\]\]/", '<a href="' . WEBROOT . '/help/view/\\1">\\2</a>', $content);

    $content = replaceImage($content, $module);
    $content = replaceVideo($content, $module);
    return $content;
}
