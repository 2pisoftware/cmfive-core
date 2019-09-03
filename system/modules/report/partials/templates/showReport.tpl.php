<?php if (!empty($form)) {
    echo $is_multicol_form ? Html::multiColForm($form) : Html::form($form);
} else {
    echo Html::alertBox('No report form data was returned', 'error');
}
