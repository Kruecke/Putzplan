<?xml version="1.0" encoding="UTF-8" ?>

<?php
    header('Content-type: application/xml');

    include "settings.php";

    $command = "make BUILDDIR=\"$builddir\" clean all 2>&1";
    exec($command, $output, $return_var);
?>

<buildinfo>
    <status>
        <?php echo $return_var; ?>
    </status>

    <log>
        <?php
            foreach ($output as $line)
                echo htmlspecialchars($line) . "\n";
        ?>
    </log>
</buildinfo>
