Set WshShell = WScript.CreateObject("WScript.Shell")
WshShell.run "%comspec% /c linuxstok.bat",0
Set WshShell = Nothing