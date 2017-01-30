<!--
<div id="users-filter">
    <form>
        <table>
            <tr><td>Show Only Active Users</td><td><input type="checkbox" name="show_active"/></td></tr>
            <tr><td colspan="2"><input type="submit" value="Filter"/></td></tr>
        </table>
    </form>
</div>
-->
<?php echo Html::box($webroot."/admin/useradd/box",__("Add New User"),true); ?>
<?php echo $table; ?>
