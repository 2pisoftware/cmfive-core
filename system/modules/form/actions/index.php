<?php

function index_ALL(Web $w)
{
    $w->ctx("title", "Forms list");
    $w->out(
        HtmlBootstrap5::b(href: "/form/edit", title: "Add a form", class: 'btn-primary') .
        HtmlBootstrap5::b(href: "/form/import", title: "Import a form", class: 'btn-secondary')
    );

    $forms = FormService::getInstance($w)->getForms();

    if (empty($forms)) {
        $w->out('<h3 class="pt-4">No forms found</h3>');
        return;
    } else {
        $w->out(HtmlBootstrap5::table(
            header: ['Title', 'Description', 'Actions'],
            data: array_map(function ($f) {
                return [
                    $f->toLink(),
                    $f->description,
                    HtmlBootstrap5::buttonGroup(
                        HtmlBootstrap5::b(href: "/form/edit/" . $f->id, title: "Edit", class: 'btn-primary') .
                        HtmlBootstrap5::b(href: "/form/export/" . $f->id, title: "Export", class: "btn-secondary") .
                        HtmlBootstrap5::b(href: "/form/delete/" . $f->id, title: "Delete", class: "btn-danger", confirm: "Are you sure you want to delete this form? (WARNING: there may be existing data saved to this form!)")
                    )
                ];
            }, $forms)
        ));
    }
}
