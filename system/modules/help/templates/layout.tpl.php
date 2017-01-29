<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" type="text/css" href="<?php echo WEBROOT; ?>/system/templates/css/style.css" />
        <script type="text/javascript" src="<?php echo WEBROOT; ?>/system/js/jquery-1.4.2.min.js" ></script>
        <script src="<?php echo WEBROOT; ?>/system/js/flowplayer/flowplayer-3.2.4.min.js"></script>
        
        
    </head>
    <body>
        <table height="600" width="100%">
            <tr>
                <td valign="top">
                    <?php if (!empty($module_toc)): ?>
                        <a href="<?php echo WEBROOT . '/help/view/' . $module_toc ?>"><?php echo $module_title; ?></a>&nbsp;:&nbsp;
                    <?php endif; ?>
                    <a href="<?php echo WEBROOT . '/help/toc'; ?>">Contents</a>&nbsp;:&nbsp;
                    <a href="<?php echo WEBROOT . '/help/view/help/onhelp'; ?>">Help on Help</a>&nbsp;:&nbsp;
                    <hr />
                </td>
            </tr>
            <tr>
                <td valign="top" height="100%"><?php echo $body; ?></td>
            </tr>
            <tr>
                <td valign="bottom"><hr/>
                    Copyright <?php echo date('Y'); ?> <a href="<?php echo $w->moduleConf('main', 'company_url'); ?>"><?php echo $w->moduleConf('main', 'company_name'); ?></a>
                </td>
            </tr>
        </table>

    </body>
    		<script type="text/javascript">

		    $(document).ready(function(){
		    	var options = { innerHeight : $( 'body' ).outerHeight( true ) };
		    	parent.$.fn.colorbox.resize( options );
		    });
		</script>
</html>	