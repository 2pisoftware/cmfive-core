<?php
$currentIndex = "";
if (!empty($results)): ?>
    <?php foreach ($results as $res):

		if ($res['class_name'] != $currentIndex):
			$currentIndex = $res['class_name']; ?>
			<hr/><div class="search-index"><?php echo $currentIndex; ?></div>
            <?php $count = 0; 
    	endif;

        $object = SearchService::getInstance($w)->getObject($res['class_name'], $res['object_id']);

        if ($object && $object->canList(AuthService::getInstance($w)->user())): ?>
            <div class="search-result">
                <?php if ($object->canView(AuthService::getInstance($w)->user())): ?>
                    <a class="search-title" href="<?php echo $webroot; ?>/<?php echo $object->printSearchUrl(); ?>">
                        <?php echo $object->printSearchTitle(); ?>
                    </a>
                    <div class="search-listing">
                        <?php echo $object->printSearchListing(); ?>
                    </div>
                <?php else: ?>
                    <div class="search-title"><?php echo $object->printSearchTitle(); ?></div>
                    <div class="search-listing">(restricted)</div>
                <?php endif; ?>
            </div>
        <?php endif;
    $count++; 
    endforeach;

    echo !empty($pagination) ? $pagination : null;
else: ?>
    <div class="search-result">
        No documents found.
    </div>
<?php endif;?>
