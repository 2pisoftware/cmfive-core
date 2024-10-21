<?php
echo HtmlBootstrap5::box('/form-application/edit', 'Create Application', class: 'btn btn-primary');
echo HtmlBootstrap5::b(href: '/form-application/import', title: 'Import Application', class: 'btn-secondary');
if (!empty($application_table_data)) :
    echo HtmlBootstrap5::table($application_table_data, null, 'tablesorter', !empty($application_table_header) ? $application_table_header : null);
else : ?>
    <h3 class="pt-4">No Applications found</h3>
<?php endif;