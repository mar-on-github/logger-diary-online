<?php
require_once __DIR__ . "/../vendor/autoload.php";

// -------------------------------------
// Functions
function CheckForUpdates()
{
    $uptodate = false;
    if (GetLoggerVer('local') == GetLoggerVer('latest')) {
        $uptodate = true;
    }
    return $uptodate;
}
function GetLoggerVer(STRING $which = 'local' | 'localf' | 'latest')
{
    if ($which === 'local') {
        return "1.1.0";
    }

    if ($which === 'localf') {
        return "1.1.0.0";
    }
    if ($which === 'latest') {
        return file_get_contents('http://api.from-mar.com/logger-diary.php?wants=lv.r');
    }
}

function SaveSettings($Setting, $Value)
{
    if (!file_exists(GetSaveFolder() . "/config/")) {
        mkdir(GetSaveFolder() . "/config/", 0777, true);
    }
    $settingfiletobeopened = GetSaveFolder() . "/config/" . $Setting . ".txt";
    file_put_contents($settingfiletobeopened, $Value);
}
function RetrieveSettings($Setting)
{
    $Value = NULL;
    $settingfiletobeopened = GetSaveFolder() . "/config/" . $Setting . ".txt";
    if (file_exists($settingfiletobeopened)) {
        $f = fopen($settingfiletobeopened, 'r');
        $Value = fgets($f);
    }
    return $Value;
}
function PythonGetTime() {
    if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0) {
        $timewouldbe = exec("\"". __DIR__ . "\\..\\..\\bin\\python-3.10.5-embed-amd64\\python.exe\" \"" . __DIR__ . "\\..\\scripts\\get_more_accurate_time.py\"");
} ELSE {
        $timewouldbe = exec("python \"" . __DIR__ . "/../scripts/get_more_accurate_time.py\"");
}
return $timewouldbe;
}
function GetSaveFolder()
{
    $homefolder = getenv("HOME");
    if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0) {
        $homefolder = exec('cmd.exe /c echo %appdata%');
    }
    $SaveFolder = get_absolute_path($homefolder . "/.logger-diary/");
    if (!file_exists($SaveFolder)) {
        mkdir($SaveFolder, 0777, true);
    }
    return $SaveFolder;
}
function get_absolute_path($path)
{
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
}
function AddEntry(STRING $Entry, STRING $Feel)
{
    $filename = GetSaveFolder() . "/entries";
    $fileContent = file_get_contents($filename);
    $EntryID = (md5(date('Y-m-d H:i:s') . rand()));
    file_put_contents($filename, $EntryID . "\n" . $fileContent);
    $EntryData = new stdClass();
    $EntryData->Date = date('Y-m-d') . " " . PythonGetTime();
    $EntryData->Text = $Entry;
    $EntryData->Feel = $Feel;
    $EntryJson = json_encode($EntryData);
    $filename = GetSaveFolder() . "/entry." . $EntryID . ".json";
    file_put_contents($filename, $EntryJson);
}

function RetrieveEntryData($EntryID, $DataType = "Date" | "Text" | "Feel")
{
    $EntryJson = file_get_contents(GetSaveFolder() . "/entry." . $EntryID . ".json");
    $EntryData = json_decode($EntryJson, true);
    $Date = $EntryData["Date"];
    $Text = $EntryData["Text"];
    $Feel = $EntryData["Feel"];
    return $$DataType;
}
//  ------------------------------------   < todo: Themes should not be part of the first release, as they slurp up my attention from the base.
// Setting themes.
if (isset($_POST['set_theme'])) {
    if ($_POST['set_theme'] == "jellybean") {
        SaveSettings("set_theme", "jellybean");
        header("Location: /settings");
    }
    if ($_POST['set_theme'] == "rouge") {
        SaveSettings("set_theme", "rouge");
        header("Location: /settings");
    }
    if ($_POST['set_theme'] == "taupe") {
        SaveSettings("set_theme", "taupe");
        header("Location: /settings");
    }
}
// If no theme is set, and no theme is known, just fall back to taupe theme.
if ((RetrieveSettings('set_theme')) === NULL) {
    SaveSettings("set_theme", "taupe");
    header("Location: ./");
}
// -------------------------------------

// -------------------------------------
// Handle resource requests
if ($_SERVER['REQUEST_URI'] === '/icon') {
    header('Content-type: image/vnd.microsoft.icon');
    readfile(__DIR__ . "/../../icons/logo.ico");
    exit;
}
if ($_SERVER['REQUEST_URI'] === '/js') {
    header('Content-type: text/javascript');
?>

<?php
    die;
}
if ($_SERVER['REQUEST_URI'] === '/logo') {
    header('Content-type: image/png');
    readfile(__DIR__ . "/../../icons/logo.png");
    exit;
}
if ($_SERVER['REQUEST_URI'] === '/style') {
    header('Content-type: text/css');
    readfile(__DIR__ . "/../styles/style.css");
    echo "\n";
    readfile(__DIR__ . "/../styles/style-" . RetrieveSettings('set_theme') . ".css");
    exit;
}
// --------------------------------------
if ($_SERVER['REQUEST_URI'] === '/add') {
    AddEntry($_POST['new_entry'], $_POST['new_entry_feel']);
    header("Location: /");
}
if ($_SERVER['REQUEST_URI'] === '/settings') {
    include(__DIR__ . "/pages/settings.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="theme-color" content="#" />
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/logo" />
    <link rel="stylesheet" href="/style" content-type="text/css" charset="utf-8" />
    <title>Logger Diary - <?php echo date('Y-m-d') . " " . PythonGetTime(); ?></title>
</head>

<body>
    <div id="theLoggerNav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="doHideNav()">&times;</a>
        <a href="/settings">Settings...</a>
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
        <?php
        if (CheckForUpdates() === false) {
            echo "<p style=\"Background-color: yellow; Border-radius: 10px; Color: #000058; Text-align: center;\">Oh! A newer logger version is available: " . GetLoggerVer('latest') . ". <a href=\"https://github.com/mar-on-github/logger-diary/releases/latest\" target=\"_blank\">Download it now.</a></p>";
        }
        ?>
        <h2>Want to let something out?</h2>
        <div class="AddEntryForm">
            <form action="/add" method="post" style="align-self: center;">
                <input type="text" name="new_entry" required><select name="new_entry_feel">
                    <option selected>...</option>
                    <option>🙂</option>
                    <option>🙁</option>
                    <option>😀</option>
                    <option>🤯</option>
                    <option>😢</option>
                    <option>😊</option>
                    <option>😕</option>
                    <option>😑</option>
                    <option>😱</option>
                    <option>😮‍💨</option>
                    <option>😮</option>
                    <option>🤪</option>
                    <option>🤬</option>
                    <option>🤮</option>
                    <option>😡</option>
                    <option>😇</option>
                    <option>👿</option>
                    <option>😝</option>
                </select><input type="submit" value="Write down">
            </form>
        </div>
        <h2>Your past entries</h2>
        <div class="readback" align="center">
            <table id="readbacktable" class="readback" align="center">
                <tr>
                    <th>When</th>
                    <th>What you wrote</th>
                    <th>How you felt</th>
                </tr>
                <?php
                $entriesfile = GetSaveFolder() . "/entries";
                if (file_exists($entriesfile)) {
                    $loggies = file($entriesfile);
                    foreach ($loggies as $DirtyEntryID) {
                        $EntryID = preg_replace(
                            "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
                            "",
                            $DirtyEntryID
                        );
                        echo "<tr>\n";
                        $Parsedown = new Parsedown();
                        echo "<td class=\"readback entry-date\">" . RetrieveEntryData($EntryID, "Date") . "</td>\n";
                        echo "<td class=\"readback entry-text\">" . $Parsedown->line(RetrieveEntryData($EntryID, "Text")) . "</td>\n";
                        echo "<td class=\"readback entry-feel\">" . RetrieveEntryData($EntryID, "Feel") . "</td>\n";
                        echo "</tr>\n";
                    }
                ?>
            </table>
        <?php
                } else {
                    echo "</table> Nothing logged yet!";
                }
        ?>
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