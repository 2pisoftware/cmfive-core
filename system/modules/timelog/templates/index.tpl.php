<div class='row-fluid'>
    <?php if (!empty($time_entries)) : ?>
        <?php foreach($time_entries as $date => $entry_struct) : ?>
            <h4 style='border-bottom: 1px solid #777;'><?php echo $date; ?><span class='right'><?php echo $w->Task->getFormatPeriod($entry_struct['total']); ?></span></h4>
            <table class='small-12'>
                <thead><tr><th width="10%"><?php _e('From'); ?></th><th width="10%"><?php _e('To'); ?></th><th width="20%"><?php _e('Object'); ?></th><th width="40%"><?php _e('Description'); ?></th><th><?php _e('Actions'); ?></th></tr></thead>
                <tbody>
                    <?php foreach($entry_struct['entries'] as $time_entry) : ?>
                        <tr>
                            <td><?php echo formatDate($time_entry->dt_start, "H:i:s"); ?></td>
                            <td><?php echo formatDate($time_entry->dt_end, "H:i:s"); ?></td>
                            <td><?php echo ($time_entry->getLinkedObject() ? get_class($time_entry->getLinkedObject()) . ": " . $time_entry->getLinkedObject()->toLink() : ''); ?></td>
                            <td><pre class="break-pre" style="font-family: sans-serif;"><?php echo $time_entry->getComment()->comment; ?></pre></td>
                            <td>
                                <?php echo $time_entry->object_class == "Task" ? Html::b('/task/edit/' . $time_entry->object_id . "#timelog", __("View Time Log")) : ""; ?>
								<?php echo Html::box('/timelog/edit/' . $time_entry->id, __('Edit'), true); ?>
								<?php echo Html::b('/timelog/delete/' . $time_entry->id, __('Delete'), __('Are you sure you want to delete this timelog?')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php else : ?>
        <h4><?php _e('No time logs found'); ?></h4>
    <?php endif; ?>
</div>

<?php if (!empty($pagination)) : ?>
	<div class="pagination-centered">
		<?php echo $pagination; ?>
	</div>
<?php endif;
