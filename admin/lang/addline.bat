@echo off

IF "%1"=="" GOTO NoArgs
IF "%2"=="" GOTO NoArgs
find "$lang['admin']['%~1']" en_US/admin.inc.php > nul
if errorlevel 1 goto newString
	echo "The variable $lang['admin']['%~1'] already exists"
	GOTO EOF

:NoArgs
echo usage: addline.bat "messagename" "new message text"
echo Note: If you need to place quotes withen "new message text" they will be escaped automatically, so you do not need to worry about them.

goto EOF

:newString

rem escaping quotes
SET newString="%~2"
SET newString=%newString:"=\"%
SET newString=%newString:~1,-2%"

for /R %%f in (*admin.inc.php) do findstr /v "?>" %%f > %%f.tmp
for /R %%f in (*admin.inc.php) do echo $lang['admin']['%~1'] = %newString%; //needs translation >> %%f.tmp
for /R %%f in (*admin.inc.php) do echo ?^> >> %%f.tmp
for /R %%f in (*admin.inc.php) do move /Y %%f.tmp %%f

:EOF