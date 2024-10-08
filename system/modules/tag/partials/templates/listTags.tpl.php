<?php if (AuthService::getInstance($w)->user()->hasRole('tag_user') && $object->canView(AuthService::getInstance($w)->user())) : ?>

<?php if ($object->canEdit(AuthService::getInstance($w)->user())) : ?>
    <div id='tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal' class='reveal-modal medium' data-reveal>
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
<?php endif; ?>

<!-- VUE IMPLEMENTATION -->
<div class='tag_container' id="tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>">
    <div class='tag_show_container' v-if='!loading && display_tags && display_tags.length'>
        <span class='info label' v-for='tag in display_tags'>{{ tag.tag }}</span>
        <span class='count_hover_tags' v-if='hidden_tags && hidden_tags.length'>+ {{ hidden_tags.length }}</span>
    </div>
    <div class="show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?>" v-if='!loading && hidden_tags && hidden_tags.length'>
        <span class='info label' v-for='tag in hidden_tags'>{{ tag.tag }}</span>
    </div>
    <span class='secondary label' v-if='!loading && (!display_tags || !display_tags.length)'>No tags</span>
    <div v-if='loading' class='loader'></div>
</div>
<script>
<?php if ($w->_layout === "layout-bootstrap-5") : ?>
    const {
        createApp
    } = Vue;
    const tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?> = createApp({
<?php else : ?>
    const tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?> = new Vue({
        el: '#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>',
<?php endif; ?>
        data: {
            display_tags: <?php echo json_encode($tags['display']); ?>,
            hidden_tags: <?php echo json_encode($tags['hover']); ?>,
            loading: false
        },
        methods: {
            getTags: async function() {
                this.loading = true;
                const response = await fetch('/tag/ajaxGetTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>');
                const json_response = await response.json();

                this.display_tags = json_response.display;
                this.hidden_tags = json_response.hover;    
                this.loading = false;
            }
        }
    <?php if ($w->_layout === "layout-bootstrap-5") : ?>
    }).mount('#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>');
    <?php else : ?>
    });
    <?php endif; ?>
    
    <?php if ($object->canEdit(AuthService::getInstance($w)->user())) : ?>
        $(document).ready(function() {
            $(document).on('close.fndtn.reveal', '[data-reveal]', function () {
                var modal = $(this);
                if (modal.attr('id') == 'tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal') {
                    tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?>.getTags();
                }
            });

            $('#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>').click(function () {
                $('#tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal').foundation('reveal', 'open', {'animation_speed': 1, 'url': '/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>'});
                return false;
            });
        });
    <?php endif; ?>
</script>

<?php endif;