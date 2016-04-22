<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Putzplan</title>
</head>

<body>
<h1>Putzplan</h1>
<?php
    // Settings. Compare to Makefile.
    $builddir = "build";
    $putzplan = "Putzplan.pdf";
    $pp_path  = "$builddir/$putzplan";
    $config   = "config.ini";
    $cfg_path = "$builddir/$config";

    if (file_exists($pp_path)) {
        echo "<p>Letzter <a href=\"$pp_path\">$putzplan</a> vom ";
        echo date("d.m.Y, H:i", filemtime($pp_path)) . " Uhr.</p>\n";
    } else {
        echo "<p>Es wurde noch kein Putzplan generiert.</p>\n";
    }
?>
<p>Aktuellen Putzplan <a href="generate.php">generieren</a>!<br/>
(Kann einige Sekunden in Anspruch nehmen.)</p>

<h2>Einstellungen</h2>
<?php
    // Reset configuration.
    if (isset($_GET["reset_config"]))
        unlink($cfg_path);

    // Initial creation of configuration file.
    if (!file_exists($cfg_path)) {
        if (!is_dir($builddir))
            mkdir($builddir, 755, true);
        copy($config, $cfg_path);
    }

    // Update configuration file.
    if (isset($_POST["config"]))
        file_put_contents($cfg_path, htmlspecialchars($_POST["config"]));
?>
<form action="" method="post">
    <textarea name="config" cols="80" rows="10"><?php echo file_get_contents($cfg_path); ?></textarea>
    <br/><input type="submit"/><input type="reset"/>
</form>
<p>Ursprüngliche Einstellungen <a href="?reset_config">wiederherstellen</a>!
(Siehe <a href="https://github.com/Kruecke/Putzplan/blob/master/config.ini">GitHub</a>)</p>

</body>
</html>
