<div class='row-fluid'>
    <?php echo $display_only !== true ? HtmlBootstrap5::box("/form-instance/edit?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id, "Add new " . $form->title, true, class: "btn btn-primary") : ''; ?>
    <?php echo $form->row_template; ?>
    <?php
    // Create paginated list of form instances
    $instances = FormService::getInstance($w)->getFormInstancesForFormAndObject($form, $object);
    $instances_array = [];
    if (!empty($instances)) {
        foreach ($instances as $instance) {
            $row_text = $instance->getTableRow();
            if (!$display_only) {
                $row_text .= '<td>' .
                    HtmlBootstrap5::box("/form-instance/edit/" . $instance->id . "?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $_bject->id, "Edit", true) .
                    HtmlBootstrap5::b("/form-instance/delete/" . $instance->id . "?redirect_url=" . $redirect_url, "Delete", "Are you sure you want to delete this item?", null, false, 'warning') .
                    '</td>';
            }

            $instances_array[] = $row_text;
        }
    }

    foreach ($instances_array as $instance) {
        echo HtmlBootstrap5::buttonGroup($instance);
    }
    ?>

    <div id="form_list_<?php echo $form->id; ?>">
        <table class='small-12'>
            <thead>
                <tr><?php echo $form->getTableHeaders(); ?>
                    <?php if ($display_only !== true) : ?>
                        <td>Actions</td><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr v-if='!instances'>
                    <td colspan="2">No form instances found</td>
                </tr>
                <tr v-if='instances' v-for="instance in instances" v-html="instance"></tr>
                <?php echo $form->getSummaryRow($object); ?>
            </tbody>
        </table>

        <ul class="pagination text-center">
            <li class="arrow" :class="{unavailable: page <= 1}"><a v-on:click="setPage(page - 1)">&laquo;</a></li>
            <li v-for="p in max_page" :class="{current: p == page}"><a v-on:click="setPage(p)">{{ p }}</a></li>
            <li class="arrow" :class="{unavailable: page >= max_page}"><a v-on:click="setPage(page + 1)">&raquo;</a></li>
        </ul>
    </div>

    <script>
        const {
            createApp
        } = Vue;
        
        createApp({
            data: {
                instances: [],
                page: 1,
                pagesize: 10,
                total_size: <?php echo $form->countFormInstancesForObject($object); ?>
            },
            computed: {
                max_page: function() {
                    return Math.ceil(this.total_size / this.pagesize);
                }
            },
            methods: {
                setPage: function(page) {
                    if (page == 0 || page == (this.max_page + 1)) {
                        return;
                    } else {
                        this.page = page;
                    }
                },
                getInstances: async function() {
                    var _this = this;
                    const response = await fetch('/form-vue/get_form_instance_rows/<?php echo $form->id; ?>/<?php echo get_class($object); ?>/<?php echo $object->id; ?>?page=' + this.page + '&pagesize=' + this.pagesize + '&display_only=<?php echo $display_only ? 1 : 0; ?>&redirect_url=<?php echo $redirect_url; ?>');
                    const _response = await response.json();
                    if (_response.success) {
                        _this.instances = _response.data;
                    } else {
                        alert(_response.error);
                    }
                }
            },
            watch: {
                page: function() {
                    this.getInstances();
                },
                instances: function() {
                    this.$nextTick(() => {
                        document.querySelectorAll('#form_list_<?php echo $form->id; ?> [data-modal-target]').forEach(function(m) {
                            m.removeEventListener('click', boundModalListener);
                            m.addEventListener('click', boundModalListener);
                        })
                    });
                }
            },
            created: function() {
                this.getInstances();
            }
        }).mount("#form_list_<?php echo $form->id; ?>");
    </script>
</div>