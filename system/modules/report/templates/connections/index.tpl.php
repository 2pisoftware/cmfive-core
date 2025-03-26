<?php
echo HtmlBootstrap5::box(href: "/report-connections/edit", title: "Add a Connection", button: true, class: "btn btn-primary");
echo !empty($connections_table) ? $connections_table : "";
