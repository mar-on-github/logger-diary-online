@echo off
echo this file should close, but not with an error.
if exist "%~dp0temp" rmdir /S /Q "%~dp0temp"
md "%~dp0temp"
(
    echo echo This script should run hidden.
    echo cd %cd%
    echo call %~dp0php.exe %*
) > "%~dp0temp\phptorun.cmd"
(
echo Set WshShell = CreateObject^("WScript.Shell"^) 
echo WshShell.Run chr^(34^) ^& "%~dp0temp\phptorun.cmd" ^& Chr^(34^), 0
echo Set WshShell = Nothing
) > "%~dp0temp\runphp.vbs"
echo running cscript...
wscript "%~dp0temp\runphp.vbs"