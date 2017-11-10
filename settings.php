<?php
    // Settings
    $builddir = getenv("CONTAINER") == "DOCKER"
        ? realpath("../build") // In Docker, baue ausserhalb des Projekt Verzeichnisses.
        : "build";             // Ansonsten einfach im Unterordner "build".
    $buildlnk = "build";
    $putzplan = "Putzplan.pdf";
    $config   = "config.ini";
?>
