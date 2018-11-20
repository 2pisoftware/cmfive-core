<?php

class EmailNotificationTemplate extends CmfiveSeedMigration {

	public $name = "EmailNotificationTemplate";
	public $description = "Installs standard task notification email template";

	public function seed() {

		///////////////////
		//// Templates ////
		///////////////////

		$email_template = new Template($this->w);
		$email_template->title = "Task Email Template";
		$email_template->module = "task";
		$email_template->category = "notification_email";
		$email_template->is_active = 1;
		$email_template->template_body = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html style="min-height: 100%; background: #f3f3f3;">
<head><span class="preheader"></span></head>
<body style="width: 100% !important; min-width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;">
<style type="text/css">
@media only screen and (max-width:596px) {
  .small-float-center {
    text-align: center !important;
  }
  .small-text-center {
    text-align: center !important;
  }
  .small-float-center {
    margin: 0 auto !important; float: none !important;
  }
  .small-text-left {
    text-align: left !important;
  }
  .small-text-right {
    text-align: right !important;
  }
  table.body table.container .hide-for-large {
    display: block !important; width: auto !important; overflow: visible !important;
  }
  table.body table.container .row.hide-for-large {
    display: table !important; width: 100% !important;
  }
  table.body table.container .show-for-large {
    display: none !important; width: 0; mso-hide: all; overflow: hidden;
  }
  td.small-1 {
    display: inline-block !important;
  }
  td.small-10 {
    display: inline-block !important;
  }
  td.small-11 {
    display: inline-block !important;
  }
  td.small-12 {
    display: inline-block !important;
  }
  td.small-2 {
    display: inline-block !important;
  }
  td.small-3 {
    display: inline-block !important;
  }
  td.small-4 {
    display: inline-block !important;
  }
  td.small-5 {
    display: inline-block !important;
  }
  td.small-7 {
    display: inline-block !important;
  }
  td.small-8 {
    display: inline-block !important;
  }
  td.small-9 {
    display: inline-block !important;
  }
  th.small-1 {
    display: inline-block !important;
  }
  th.small-10 {
    display: inline-block !important;
  }
  th.small-11 {
    display: inline-block !important;
  }
  th.small-12 {
    display: inline-block !important;
  }
  th.small-2 {
    display: inline-block !important;
  }
  th.small-3 {
    display: inline-block !important;
  }
  th.small-4 {
    display: inline-block !important;
  }
  th.small-5 {
    display: inline-block !important;
  }
  th.small-7 {
    display: inline-block !important;
  }
  th.small-8 {
    display: inline-block !important;
  }
  th.small-9 {
    display: inline-block !important;
  }
  table.body img {
    width: auto !important; height: auto !important;
  }
  table.body center {
    min-width: 0 !important;
  }
  table.body .container {
    width: 95% !important;
  }
  table.body .column {
    height: auto !important; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; padding-left: 16px !important; padding-right: 16px !important;
  }
  table.body .columns {
    height: auto !important; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; padding-left: 16px !important; padding-right: 16px !important;
  }
  table.body .collapse .column {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  table.body .collapse .columns {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  table.body .column .column {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  table.body .column .columns {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  table.body .columns .column {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  table.body .columns .columns {
    padding-left: 0 !important; padding-right: 0 !important;
  }
  td.small-1 {
    width: 8.33333% !important;
  }
  th.small-1 {
    width: 8.33333% !important;
  }
  td.small-2 {
    width: 16.66667% !important;
  }
  th.small-2 {
    width: 16.66667% !important;
  }
  td.small-3 {
    width: 25% !important;
  }
  th.small-3 {
    width: 25% !important;
  }
  td.small-4 {
    width: 33.33333% !important;
  }
  th.small-4 {
    width: 33.33333% !important;
  }
  td.small-5 {
    width: 41.66667% !important;
  }
  th.small-5 {
    width: 41.66667% !important;
  }
  td.small-6 {
    display: inline-block !important; width: 50% !important;
  }
  th.small-6 {
    display: inline-block !important; width: 50% !important;
  }
  td.small-7 {
    width: 58.33333% !important;
  }
  th.small-7 {
    width: 58.33333% !important;
  }
  td.small-8 {
    width: 66.66667% !important;
  }
  th.small-8 {
    width: 66.66667% !important;
  }
  td.small-9 {
    width: 75% !important;
  }
  th.small-9 {
    width: 75% !important;
  }
  td.small-10 {
    width: 83.33333% !important;
  }
  th.small-10 {
    width: 83.33333% !important;
  }
  td.small-11 {
    width: 91.66667% !important;
  }
  th.small-11 {
    width: 91.66667% !important;
  }
  td.small-12 {
    width: 100% !important;
  }
  th.small-12 {
    width: 100% !important;
  }
  .column td.small-12 {
    display: block !important; width: 100% !important;
  }
  .column th.small-12 {
    display: block !important; width: 100% !important;
  }
  .columns td.small-12 {
    display: block !important; width: 100% !important;
  }
  .columns th.small-12 {
    display: block !important; width: 100% !important;
  }
  table.body td.small-offset-1 {
    margin-left: 8.33333% !important;
  }
  table.body th.small-offset-1 {
    margin-left: 8.33333% !important;
  }
  table.body td.small-offset-2 {
    margin-left: 16.66667% !important;
  }
  table.body th.small-offset-2 {
    margin-left: 16.66667% !important;
  }
  table.body td.small-offset-3 {
    margin-left: 25% !important;
  }
  table.body th.small-offset-3 {
    margin-left: 25% !important;
  }
  table.body td.small-offset-4 {
    margin-left: 33.33333% !important;
  }
  table.body th.small-offset-4 {
    margin-left: 33.33333% !important;
  }
  table.body td.small-offset-5 {
    margin-left: 41.66667% !important;
  }
  table.body th.small-offset-5 {
    margin-left: 41.66667% !important;
  }
  table.body td.small-offset-6 {
    margin-left: 50% !important;
  }
  table.body th.small-offset-6 {
    margin-left: 50% !important;
  }
  table.body td.small-offset-7 {
    margin-left: 58.33333% !important;
  }
  table.body th.small-offset-7 {
    margin-left: 58.33333% !important;
  }
  table.body td.small-offset-8 {
    margin-left: 66.66667% !important;
  }
  table.body th.small-offset-8 {
    margin-left: 66.66667% !important;
  }
  table.body td.small-offset-9 {
    margin-left: 75% !important;
  }
  table.body th.small-offset-9 {
    margin-left: 75% !important;
  }
  table.body td.small-offset-10 {
    margin-left: 83.33333% !important;
  }
  table.body th.small-offset-10 {
    margin-left: 83.33333% !important;
  }
  table.body td.small-offset-11 {
    margin-left: 91.66667% !important;
  }
  table.body th.small-offset-11 {
    margin-left: 91.66667% !important;
  }
  table.body table.columns td.expander {
    display: none !important;
  }
  table.body table.columns th.expander {
    display: none !important;
  }
  table.body .right-text-pad {
    padding-left: 10px !important;
  }
  table.body .text-pad-right {
    padding-left: 10px !important;
  }
  table.body .left-text-pad {
    padding-right: 10px !important;
  }
  table.body .text-pad-left {
    padding-right: 10px !important;
  }
  table.menu {
    width: 100% !important;
  }
  table.menu td {
    width: auto !important; display: inline-block !important;
  }
  table.menu th {
    width: auto !important; display: inline-block !important;
  }
  table.menu.small-vertical td {
    display: block !important;
  }
  table.menu.small-vertical th {
    display: block !important;
  }
  table.menu.vertical td {
    display: block !important;
  }
  table.menu.vertical th {
    display: block !important;
  }
  table.menu[align=center] {
    width: auto !important;
  }
}
</style>
<table class="body" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; height: 100%; width: 100%; min-width:100%; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; line-height: 19px; font-size: 16px; background: #f3f3f3; margin: 0; padding: 0;" bgcolor="#f3f3f3"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<td class="center" align="center" valign="top" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; line-height: 19px; font-size: 16px; margin: 0; padding: 0;">
          <table class="container" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: inherit; width: 580px; min-width: 580px; background: #fefefe; margin: 0 auto; padding: 0;" bgcolor="#fefefe"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left" valign="top">
            <table class="row cmfive-heading" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; position: relative; display: table; background: #444; padding: 0;" bgcolor="#444"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th class="small-12 columns first" style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0 auto; padding: 0px;" align="left">
                <table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left">
                      <center data-parsed="" style="width: 100%; min-width: 100%;">
                        <img class="small-float-center float-center" src="{{ logo_url }}" align="center" style="clear: both; text-decoration: none; outline: 0; -ms-interpolation-mode: bicubic; width: auto; max-width: 300px; display: block; float: none; text-align: center; margin: 0 auto;">
</center>
                    </th>
                  </tr></table>
</th>
            </tr></tbody></table>
<center data-parsed="" style="width: 100%; min-width: 100%;">
              <table class="container float-center" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: center; width: 580px; min-width: 580px; float: none; background: #fefefe; margin: 0 auto; padding: 0;" bgcolor="#fefefe"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left" valign="top">
                <table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width: 100%; position: relative; display: table; padding: 0;"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th class="cmfive-status-heading small-12 large-12 columns first last" style="width: 564px; min-width: 564px; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; background: #efefef; margin: 0 auto; padding: 16px;" align="left" bgcolor="#efefef">
                    <table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left">
                            <span align="center" class="float-center" style="line-height: normal; height: 100%; font-size: 26pt; font-weight: lighter; color: #444; text-align: center; display: inline-block; width: 100%; min-width:100%;">{{ status }}</span>
                        </th>
                        <th class="expander" style="visibility: hidden; width: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left"></th>
                      </tr></table>
</th>
                  </tr></tbody></table>
<table class="row collapse" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; position: relative; display: table; padding: 0;"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th class="small-12 large-12 columns first last" style="width: 588px; min-width: 588px;color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0 auto; padding: 0 0 16px;" align="left">
<table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; padding: 0;">
  {% for title, value in fields %}
  {% if value %}
    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
    <th style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left">
      <table class="row cmfive-data-row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; position: relative; display: table; padding: 0;"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
        <th class="cmfive-data-row-heading small-12 large-6 columns first" style="width: 50%; min-width:50%; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; border-top-color: #000; border-top-width: 1px; border-top-style: solid; vertical-align: middle; background: #cdcdcd; margin: 0 auto; padding: 15px 20px 16px 16px;" align="left" bgcolor="#cdcdcd" valign="middle">
          <span class="small-text-center large-text-right" style="width: 100%; min-width:100%; display: inline-block; font-size: 18pt; font-weight: lighter; line-height: normal; text-align: right; color: #000;">{{ title }}</span>
        </th>
    <th class="cmfive-data-row-value small-12 large-6 columns last" style="width: 50%; min-width:50%; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; border-top-color: #000; border-top-width: 1px; border-top-style: solid; margin: 0 auto; padding: 15px 16px 16px 20px;" align="left">
      <table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width: 100%; padding: 0;">
        <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
          <th style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left">
            <span class="small-text-center" style="width: 100%; min-width: 100%; display: inline-block; font-size: 18pt; font-weight: lighter; line-height: normal;">{{ value | raw }}</span>
          </th>
        </tr>
      </table>
    </th>
    </tr>
    </tbody></table>
  </th>
  </tr>
  {% endif %}
  {% endfor %}
</table>
</th>
                  </tr></tbody></table>
                {% if footer %}
                <table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width: 100%; position: relative; display: table; padding: 0;"><tbody><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th class="small-12 large-12 columns first last" style="width: 564px; min-width: 564px; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0 auto; padding: 0 16px 16px;" align="left">
                    <table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width: 100%; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
<th style="color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left">
                          Description: {{ footer | raw }}
                        </th>
                        <th class="expander" style="visibility: hidden; width: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left"></th>
                      </tr></table>
</th>
                  </tr></tbody></table>
                {% endif %}
                {% if can_view_task | default(false) %}
                <table class="row" style="background-color: #5eb2e1; border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width: 100%; position: relative; display: table; padding: 0;">
                    <tbody>
                      <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
              <th class="cmfive-button small-12 large-12 columns first last" style="width: 564px; min-width: 564px; color: #0a0a0a; margin: 0 auto;" align="left">
                            <table style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; min-width:100%; padding: 0;">
                                    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <th style="margin: 0; padding: 0;" align="left">
                                          <center data-parsed="" style="width: 100%; min-width: 100%;">
                                            <a class="button expand float-center" style="text-align: center; padding: 16px 0px; text-decoration: none; color: white; font-family: Helvetica, Arial, sans-serif; font-weight: 400; line-height: 1.3; margin: 0 0 0px; height: 100%; width: 100%; min-width:100%; display: inline-block;" target="_blank" href="{{ action_url }}" align="center">View this task</a>
                                          </center>
                                        </th>
                                  <th class="expander" style="visibility: hidden; width: 0; color: #0a0a0a; font-family: Helvetica, Arial, sans-serif; font-weight: 400; text-align: left; line-height: 19px; font-size: 16px; margin: 0; padding: 0;" align="left"></th>
                              </tr>
                                </table>
              </th>
                      </tr>
                    </tbody>
                </table>
                {% endif %}
                </td></tr></tbody></table>
</center>
            </td></tr></tbody></table>
</td>
      </tr></table>
<!-- prevent Gmail on iOS font size manipulation --><div style="display: none; white-space: nowrap; font: 15px courier;">                                                             </div>
</body>
</html>
';

    $email_template->test_body_json = '{"status":"[42] New task created","footer":"<p>The president has voiced the urgency to finish the detector ASAP, little does she know I have no idea what I\'m doing, people in the fleet are starting to attack one another!<\/p>","action_url":"http:\/\/cmfive.com","logo_url":"http:\/\/cmfive.com\/wp-content\/uploads\/2014\/05\/cmfive-logo-for-header.png","fields":{"Assigned to":"Gaius Baltar","Type":"Support Ticket","Title":"Finish Cylon Detector","Due":"04-05-2092","Status":"New","Priority":"Critical"},"can_view_task":true}';

		$email_template->insert();
	}

}
