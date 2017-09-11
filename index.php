<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>Putzplan</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container">
<div class="page-header">
    <h1>Putzplan</h1>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
                <strong>Download</strong>
            </div>
            <div class="panel-body">
                <?php
                    // Settings. Compare to Makefile.
                    $builddir = "build";
                    $putzplan = "Putzplan.pdf";
                    $pp_path  = "$builddir/$putzplan";
                    $config   = "config.ini";
                    $cfg_path = "$builddir/$config";

                    if (file_exists($pp_path)) {
                        echo "<a href=\"$pp_path\">$putzplan</a>\n";
                        echo "vom " . date("d.m.Y, H:i", filemtime($pp_path)) . " Uhr.\n";
                    } else {
                        echo "Es wurde noch kein Putzplan generiert.\n";
                    }
                ?>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                <strong>Aktualisieren</strong>
            </div>
            <div class="panel-body">
                <script>
                    function generate() {
                        document.getElementById("buildbutton").setAttribute("disabled", "disabled");

                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                var status = this.responseXML.getElementsByTagName("status")[0].childNodes[0].nodeValue.trim();
                                var bldlog = this.responseXML.getElementsByTagName("log")[0].childNodes[0].nodeValue.trim();

                                // Display build status
                                if (status == 0) {
                                    document.getElementById("buildstatus").innerHTML =
                                        "<div class=\"alert alert-success\" role=\"alert\" style=\"margin-bottom:0;\">" +
                                        "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span> " +
                                        <?php echo "\"<a href=\\\"$pp_path\\\">$putzplan</a>\""; ?> +
                                        " erfolgreich erstellt!</div>";
                                } else {
                                    document.getElementById("buildstatus").innerHTML =
                                        "<div class=\"alert alert-danger\" role=\"alert\" style=\"margin-bottom:0;\">" +
                                        "<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span> " +
                                        "Fehlgeschlagen! Fehler code: " + status + "</div>";
                                }

                                // Display build log
                                document.getElementById("buildlog").innerHTML =
                                    "<div class=\"panel panel-default\">" +
                                    "<div class=\"panel-heading\"><strong>Build log</strong></div>" +
                                    "<div class=\"panel-body\"><textarea class=\"form-control\"" +
                                    " rows=\"10\" readonly>" + bldlog + "</textarea></div></div>";

                                document.getElementById("buildbutton").removeAttribute("disabled");
                            }
                        };
                        xhttp.open("GET", "generate.php", true);
                        xhttp.send();
                    }
                </script>
                <p><button id="buildbutton" type="button" class="btn btn-default" onclick="generate()">Putzplan generieren!</button></p>
                <div id="buildstatus">
                    <p>(Kann einige Sekunden in Anspruch nehmen.)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                <strong>Einstellungen</strong>
            </div>
            <div class="panel-body">
                <?php
                    // Reset configuration.
                    if (isset($_GET["reset_config"]))
                        unlink($cfg_path);

                    // Initial creation of configuration file.
                    if (!file_exists($cfg_path)) {
                        if (!is_dir($builddir))
                            mkdir($builddir, 0755, true);
                        copy($config, $cfg_path);
                    }

                    // Update configuration file.
                    if (isset($_POST["config"]))
                        file_put_contents($cfg_path, htmlspecialchars($_POST["config"]));
                ?>
                <form action="" method="post">
                    <textarea class="form-control" rows="10" id="config" name="config"><?php echo file_get_contents($cfg_path); ?></textarea>
                    <div class="btn-group">
                        <input type="submit" class="btn btn-default">
                        <input type="reset" class="btn btn-default">
                    </div>
                </form>
            </div>
            <div class="panel-footer">
                Ursprüngliche Einstellungen <a href="?reset_config">wiederherstellen</a>!
                (Siehe <a href="https://github.com/Kruecke/Putzplan/blob/master/config.ini">GitHub</a>)
            </div>
        </div>
    </div>
</div>

<div id="buildlog"></div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="bootstrap/js/bootstrap.min.js"></script>
</div>
</body>
</html>
