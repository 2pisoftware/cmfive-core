<ul>
<?php if(!empty($_SESSION['install']['saved']['application_name'])) : ?>
    <li><b>Application name:</b> "<?= $_SESSION['install']['saved']['application_name'] ?>"</li>
<?php endif;
    if(!empty($_SESSION['install']['saved']['company_name'])) : ?>
    <li><b>Company name:</b> "<?= $_SESSION['install']['saved']['company_name'] ?>"</li>
<?php endif;
    if(!empty($_SESSION['install']['saved']['company_url'])) : ?>
    <li><b>Company url:</b> <a href="<?= $_SESSION['install']['saved']['company_url'] ?>"><?= $_SESSION['install']['saved']['company_url'] ?></a></li>
<?php endif;
    if(!empty($_SESSION['install']['saved']['company_support_email'])) : ?>
    <li><b>Company email:</b> <a href="mailto:<?= $_SESSION['install']['saved']['company_support_email'] ?>"><?= $_SESSION['install']['saved']['company_support_email'] ?></a></li>
<?php endif; ?>
</ul>
