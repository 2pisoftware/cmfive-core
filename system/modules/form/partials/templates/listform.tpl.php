<div class='row'>
    <div class='col'>
        <?php
        $page = 1;
        $pagesize = 50;
        echo $display_only !== true ? HtmlBootstrap5::box(
            href: "/form-instance/edit?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id,
            title: "Add new " . $form->title,
            button: true,
            class: "btn btn-sm btn-primary"
        ) : '';

        // $headers = $form->getTableHeadersAsArray();
        // if ($display_only !== true) {
        //     array_push($headers, "Actions");
        // }
        $headers = $form->getTableHeaders();
        if ($display_only !== true) {
            $headers .= "<td>Actions</td>";
        }

        $data = $form->getFormInstancesForObject($object);
        $instances = [];
        foreach ($data as $instance) {
            array_push(
                $instances,
                "<tr>" . $instance->getTableRow() . (!$display_only ? '<td class="p-1">' .
                    HtmlBootstrap5::box(
                        href: "/form-instance/edit/" . $instance->id . "?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id,
                        title: "Edit",
                        button: true,
                        class: "btn btn-sm btn-secondary"
                    ) .
                    HtmlBootstrap5::b(
                        href: "/form-instance/delete/" . $instance->id . "?redirect_url=" . $redirect_url,
                        title: "Delete",
                        confirm: "Are you sure you want to delete this item?",
                        class: 'btn btn-sm btn-error'
                    ) .
                    '</td>' : '') . "</tr>"
            );
        }
        ?>
        <table class="table table-striped">
            <thead><?php echo $headers; ?></thead>
            <tbody>
                <?php echo implode('', $instances); ?>
            </tbody>
        </table>

        <?php
        // echo HtmlBootstrap5::paginatedTable(
        //     header: $headers,
        //     data: array_map(fn (FormInstance $f) => [], $data),
        //     page: 1,
        //     page_size: 50,
        //     total_results: $form->countFormInstancesForObject($object),
        //     base_url: $redirect_url,
        // );
        
        // echo HtmlBootstrap5::pagination($currentpage, $numpages, $pagesize, $totalresults, $redirect_url); ?>
    </div>
</div>