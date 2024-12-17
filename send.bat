@echo off
:: Get current date and time
for /f "tokens=2 delims==" %%I in ('"wmic os get localdatetime /value"') do set datetime=%%I
set year=%datetime:~0,4%
set month=%datetime:~4,2%
set day=%datetime:~6,2%
set hour=%datetime:~8,2%
set minute=%datetime:~10,2%
set second=%datetime:~12,2%

:: Format the date and time for the commit message
set datetime_formatted=%year%-%month%-%day% %hour%:%minute%:%second%

:: Add all changes
git add .

:: Commit changes with the formatted date and time
git commit -m "publish %datetime_formatted%"

:: Push changes to the remote repository
git push

:: Pause to view the output
pause
