<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Putzplan</title>
</head>

<body>
<?php
    // Settings. Compare to Makefile.
    $builddir = "build";
    $putzplan = "Putzplan.pdf";
    $pp_path  = "$builddir/$putzplan";

    $command = "make clean $pp_path 2>&1";
    exec($command, $output, $return_var);

    if ($return_var == 0) {
        echo "<h1>Generierung erfolgreich! :)</h1>\n";
        echo "<p><strong>Download</strong>: <a href=\"$pp_path\">$putzplan</a>\n";
        echo "vom " . date("d.m.Y, H:i", filemtime($pp_path)) . " Uhr.</p>\n";
    } else {
        echo "<h1>Generierung fehlgeschlagen! :(</h2>\n";
        echo "<p>Schau' dir die Log Ausgaben an, vielleicht kannst du den Fehler bereits erkennen.<br/>\n";
        echo "Ansonsten melde den Fehler mit möglichst genauer Beschreibung bei\n";
        echo "<a href=\"https://github.com/Kruecke/Putzplan/issues\">GitHub</a>.</p>\n";
    }
?>
<p><a href="index.php">Zurück</a> zur Startseite.</p>

<h2>Log Ausgabe</h2>
<samp>
<?php
    foreach ($output as $line)
        echo htmlspecialchars($line) . "<br/>\n";
?>
</samp>
</body>
</html>
