<?php if (AuthService::getInstance($w)->user()->hasRole('tag_user') && $object->canView(AuthService::getInstance($w)->user())) : ?>

    <style>
        .hidden_tags {
            display: none;
        }

        .tags-container {
            display: flex;
            align-items: center;
            margin-left: 0.5rem;
            display: inline-block;
        }

        .tags-container > #hidden_tags {
            display: inline;
        }

        .tag-small {
            font-size: 0.7rem;
            padding: 2px 3px 2px 3px;
        }
    </style>

    <div class="tags-container" data-modal-target="/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>">
        <div id="tags_<?php echo $tag_obj_id ?>">
        </div>

        <div id="hidden_tags_<?php echo $tag_obj_id ?>" class="hidden_tags">
        </div>
    </div>

    <script>
        // wrap in an IIFE so that multiple tag partials don't conflict with eachother
        // TODO: it would be better to just have a single script to manage the entire pages tags, like modals have
        (() => {
            const id = "<?php echo $tag_obj_id ?>";

            // TODO: Htmlbootstrap5::table renders TWO tables, one for desktop and the other for mobile
            // it also duplicates all the content, which means this partial gets rendered twice
            // which means we need a way to track which version should get executed
            window.tag_managers = window.tag_managers ?? [];
            if (window.tag_managers.indexOf(id) !== -1) return;
            window.tag_managers.push(id);

            const tags = {
                display: <?php echo json_encode($tags['display']); ?>,
                hidden: <?php echo json_encode($tags['hover']); ?>,
            };

            const createTagElement = (tag) => {
                const text = typeof tag === "string" ? tag : tag.tag;

                const elem = document.createElement("span");
                elem.innerText = text;
                elem.classList.add("bg-secondary", "tag-small")
                
                return elem;
            }

            const update = () => {
                const shown = document.getElementById(`tags_${id}`);
                const hidden = document.getElementById(`tags_${id}`);

                if (tags.display.length === 0) {
                    return shown.appendChild(createTagElement("No tags"));
                }

                tags.display.forEach(tag => shown.appendChild(createTagElement(tag)));
                tags.hidden.forEach(tag => shown.appendChild(createTagElement(tag)));
            }

            // TODO: trigger a reload of tags when modal closes

            update();
        })();
    </script>

<?php endif;
