<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="theme-color" content="#" />
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/logo" />
    <link rel="stylesheet" href="/style" content-type="text/css" charset="utf-8" />
    <title>Logger Diary - <?php echo date('Y-m-d H:i'); ?></title>
</head>

<body>
    <div id="theLoggerNav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="doHideNav()">&times;</a>
        <a href="/">Back to Logger</a>
        <a href="javascript:window.close()">Exit Logger</a>
        <a href="javascript:void(0)" id="mmlt" style="display: block;" onclick="moreLinks()">➕ more...</a>
        <div style="display: none;" id="menumorelinks">
            <?php
            echo file_get_contents('http://api.from-mar.com/logger-diary.php?wants=links.more');
            ?>
            <a href="javascript:void(0)" onclick="lessLinks()">➖ less</a>
        </div>


    </div>
    <div id="main">
        <span onclick="doViewNav()"><button style="border-color: #000000; border-radius: 20; background-color:aquamarine; font-size: 20px; display: block;" id="ViewNavButton">&#9776;</button></span>
        <h1>Logger, the simple digital diary.</h1>
        <h4>By Mar Bloeiman</h4>
        <h3>Settings</h3>
        <?php
        if (CheckForUpdates() === false) {
            echo "<p style=\"Background-color: yellow; Border-radius: 10px; Color: #000058; Text-align: center;\">Oh! A newer logger version is available: " . GetLoggerVer('latest') . ". <a href=\"https://github.com/mar-on-github/logger-diary/releases/latest\" target=\"_blank\">Download it now.</a></p>";
        }
        ?>
        <div class="AddEntryForm">
            <p style="Text-align: center;">Want to <a href="/">go back</a>?</p>
        </div>
        <div class="readback settingsmain" align="center">
            <form id="setstyles" action="/" method="POST">
                <h4>Change Logger theme</h4>
                <div class="radiotoggle">
                    <input type="radio" name="set_theme" value="taupe" id="theme-selector-taupe" />
                    <label for="theme-selector-taupe" id="visual-theme-selector-taupe"> <img src="/source/img/taupe-preview.gif" width="100%">Taupe (default)</label>
                    <input type="radio" name="set_theme" value="jellybean" id="theme-selector-jellybean" />
                    <label for="theme-selector-jellybean" id="visual-theme-selector-jellybean"> <img src="/source/img/jellybean-preview.gif" width="100%">Jelly Bean Blue (light)</label>
                    <input type="radio" name="set_theme" value="rouge" id="theme-selector-rouge" />
                    <label for="theme-selector-rouge" id="visual-theme-selector-rouge"> <img src="/source/img/rouge-preview.gif" width="100%">Rouge (dark)</label>
                </div>
                <button type="submit">Set theme</button>
            </form>
        </div>
        <footer class="infofooter">
            <hr>
            <p><?php echo "Files are saved in: '" . GetSaveFolder() . "'. Currently used theme: '" . RetrieveSettings('set_theme') . "'. Using Logger version: <b>" . GetLoggerVer('local') . "</b>"; ?> </p>
        </footer>
    </div>
    <script>
        function doViewNav() {
            document.getElementById("theLoggerNav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
            document.getElementById("ViewNavButton").style.display = "none";
        }

        function doHideNav() {
            document.getElementById("theLoggerNav").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
            document.getElementById("ViewNavButton").style.display = "block";
        }

        function moreLinks() {
            document.getElementById("menumorelinks").style.display = "block";
            // mmlt: menu more links trigger
            document.getElementById("mmlt").style.display = "none";
        }

        function lessLinks() {
            document.getElementById("menumorelinks").style.display = "none";
            document.getElementById("mmlt").style.display = "block";
        }
    </script>
</body>