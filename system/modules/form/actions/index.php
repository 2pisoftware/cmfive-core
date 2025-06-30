<?php

function index_ALL(Web $w)
{
    $w->ctx("title", "Forms list");
    $w->out(
        HtmlBootstrap5::box("/form/edit", "Add a form", class: 'btn btn-primary') .
        HtmlBootstrap5::box("/form/import", "Import a form", class: 'btn btn-secondary')
    );

    $forms = FormService::getInstance($w)->getForms();

    if (empty($forms)) {
        $w->out('<h3 class="pt-4">No forms found</h3>');
        return;
    } else {
        $w->out(HtmlBootstrap5::table(
            header: ['Title', 'Description', 'Actions'],
            data: array_map(function ($f) use ($w) {
                return [
                    '<span class="text-break">' . $f->toLink() . '</span>',
                    '<p class="text-break">' . StringSanitiser::sanitise($f->description) . '</p>',
                    HtmlBootstrap5::buttonGroup(
                        HtmlBootstrap5::box(href: "/form/edit/" . $f->id, title: "Edit", class: 'btn btn-primary') .
                        HtmlBootstrap5::b(href: "/form/export/" . $f->id, title: "Export", class: "btn-secondary") .
                        HtmlBootstrap5::b(href: "/form/delete/" . $f->id, title: "Delete", class: "btn-danger", confirm: "Are you sure you want to delete this form? (WARNING: there may be existing data saved to this form!)")
                    )
                ];
            }, $forms)
        ));
    }
}
