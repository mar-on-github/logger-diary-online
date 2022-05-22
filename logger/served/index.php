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

function AddEntry(STRING $Entry)
{
    $filename = GetSaveFolder() . "/loggies.log";
    $fileContent = file_get_contents($filename);
    $Input = date('Y-m-d H:i:s') . "->" . $Entry;
    file_put_contents($filename, $Input . "\n" . $fileContent);
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
if ($_SERVER['REQUEST_URI'] === '/logo') {
    header('Content-type: image/png');
    readfile(__DIR__ . "/../../icons/logo.png");
    exit;
}
if ($_SERVER['REQUEST_URI'] === '/style') {
    header('Content-type: text/css');
    readfile(__DIR__ . "/../styles/style.css");
    readfile(__DIR__ . "/../styles/style-" . RetrieveSettings('set_theme') . ".css");
    exit;
}
// --------------------------------------
if ($_SERVER['REQUEST_URI'] === '/add') {
    AddEntry($_POST['new_entry']);
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
        <form action="/add" method="post">
            <input type="text" name="new_entry"><input type="submit" value="Write down">
        </form>
    </div>
    <div class="readback">
        <ul>
        <?php
            $filename = GetSaveFolder() . "/loggies.log";
            $loggies = file($filename);
            foreach ($loggies as $entry) {
            echo "<li>" . $entry . "</i>";
            }
        ?>
        </ul>
    </div>
    <div class="infofooter">
        <p><?php echo "Files are saved in: '" . GetSaveFolder() . "'. Currently used theme: '" . RetrieveSettings('set_theme') . "'. Last update: <b>may 22th '22.</b>"; ?> </p>
    </div>
</body>