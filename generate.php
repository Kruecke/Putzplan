<?xml version="1.0" encoding="UTF-8" ?>
<?php
    header('Content-type: application/xml');

    // Settings. Compare to Makefile.
    $builddir = "build";
    $putzplan = "Putzplan.pdf";
    $pp_path  = "$builddir/$putzplan";

    $command = "make clean $pp_path 2>&1";
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
