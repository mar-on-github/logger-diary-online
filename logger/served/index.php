<?php
// require_once __DIR__ . "/../vendor/autoload.php";

// -------------------------------------
// Functions

function SaveSettings(mixed $Setting, string $Value)
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
function GetSaveFolder()
{
    $homefolder = getenv("HOME");
    $SaveFolder = $homefolder . "/.logger-diary";
    if (!file_exists($SaveFolder)) {
        mkdir($SaveFolder, 0777, true);
    }
    return $SaveFolder;
}

function AddEntry(STRING $Entry, STRING $Feel)
{
    $filename = GetSaveFolder() . "/entries";
    $fileContent = file_get_contents($filename);
    $EntryID = (md5(date('Y-m-d H:i:s') . rand()));
    file_put_contents($filename, $EntryID . "\n" . $fileContent);
    $EntryData = new stdClass();
    $EntryData->Date = date('Y-m-d H:i');
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
if (isset($_REQUEST['set_theme'])) {
    if ($_REQUEST['set_theme'] == "light") {
        SaveSettings("set_theme", "light");
        header("Location: ./");
    }
    if ($_REQUEST['set_theme'] == "dark") {
        SaveSettings("set_theme", "dark");
        header("Location: ./");
    }
    if ($_REQUEST['set_theme'] == "taupe") {
        SaveSettings("set_theme", "taupe");
        header("Location: ./");
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
?>
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
    <h1>Logger</h1>
    <h4>By Mar Bloeiman</h4>
    <div class="AddEntryForm">
        <form action="/add" method="post" style="align-self: center;">
            <input type="text" name="new_entry" required><select name="new_entry_feel">
                <option value="😀" selected>😀</option>
                <option value="🤯">🤯</option>

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
            $filename = GetSaveFolder() . "/entries";
            $loggies = file($filename);
            foreach ($loggies as $DirtyEntryID) {
                $EntryID = preg_replace(
                    "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
                    "",
                    $DirtyEntryID
                );
                echo "<tr>\n";
                echo "<td class=\"readback entry-date\">" . RetrieveEntryData($EntryID, "Date") . "</td>\n";
                echo "<td class=\"readback entry-text\">" . RetrieveEntryData($EntryID, "Text") . "</td>\n";
                echo "<td class=\"readback entry-feel\">" . RetrieveEntryData($EntryID, "Feel") . "</td>\n";
                echo "</tr>\n";
            }
            ?>
            </ul>
        </table>
    </div>
    <footer class="infofooter">
        <p><?php echo "Files are saved in: '" . GetSaveFolder() . "'. Currently used theme: '" . RetrieveSettings('set_theme') . "'. Last update: <b>may 22th '22.</b>"; ?> </p>
    </footer>
</body>