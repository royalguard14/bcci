@echo off
:: Add all changes
git add .

:: Commit changes with a default message
git commit -m "publish"

:: Push changes to the remote repository
git push

:: Pause to view the output
pause
