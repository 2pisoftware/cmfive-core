<b>Users Currently Logged In</b>
<ul>
    <?php if (!empty($currentUsers)):foreach($currentUsers as $u):?>
        <li>
            <?php echo $u->getFullName();?>
        </li>
    <?php endforeach;endif;?>
</ul>
