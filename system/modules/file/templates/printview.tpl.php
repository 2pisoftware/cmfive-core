<html>
    <body>
        <?php $col=0; $row=0;?>
        <?php foreach ($attachments as $att) : ?>
            <?php if ($att->isImage()) :
                if ($row === 0) {
                    echo "<table>\n";
                }
                if ($col === 0) {
                    echo "<tr>\n";
                }
                $col++;$row++;
                if ($col == $cmax) {
                    echo "</tr>\n";
                    $col = 0;
                }
                if ($row == $rmax) {
                    echo "</table>\n";
                    $row = 0;
                }
                ?>
                <td><img src="<?php echo $webroot."/file/atfile/".$att->id."/".$att->filename; ?>" border="0"/></td>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php 
            if ($col != 0) {
                echo "</tr>\n";
            }
            if ($row != 0) {
                echo "</table>\n";
            }
        ?>
    <body>
</html>
