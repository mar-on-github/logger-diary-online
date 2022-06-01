echo opejf
pause
set phptorun=%~dp0php.exe %*
echo Running: %phptorun%
md %~dp0temp
(
    echo cd %cd%
    echo call %phptorun%
) > "%~dp0temp\phptorun.cmd"
(
echo Set WshShell = CreateObject^("WScript.Shell"^) 
echo WshShell.Run chr^(34^) ^& "%~dp0temp\phptorun.cmd" ^& Chr^(34^), 0
echo Set WshShell = Nothing
) > "%~dp0temp\runphp.vbs"
start wscript "%~dp0temp\runphp.vbs"