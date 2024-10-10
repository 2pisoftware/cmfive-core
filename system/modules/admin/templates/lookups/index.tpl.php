<div class='tabs mt-4'>
    <div class='tab-head'>
        <a class='active' href="#dynamic">Dynamic Lookup</a>
        <a href="#countries">Countries</a>
    </div>
    <div class='tab-body'>
        <div id='dynamic'>
            <?php
            echo HtmlBootstrap5::box(href: "/admin-lookups/edit", title: "Create Lookup", class: 'btn btn-primary');
            echo HtmlBootstrap5::filter("Search Lookup Items", [
                ["Type", "select", "type", Request::string('type'), LookupService::getInstance($w)->getLookupTypes(), "form-select"]
            ], "/admin-lookups");
            echo $listitem; ?>
        </div>
        <div id='countries'>
            <?php
            echo HtmlBootstrap5::box(href: "/admin-lookups/edit_country", title: "Create Country", class: 'btn btn-primary');
            echo $country_rows; ?>
        </div>
    </div>
</div>