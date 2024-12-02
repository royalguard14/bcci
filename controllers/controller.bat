@echo off
set /p classname=Enter the controller name (e.g., Sample): 

set filename=%classname%Controller.php

echo ^<?php > %filename%
echo require_once 'BaseController.php'; >> %filename%
echo. >> %filename%
echo class %classname%Controller extends BaseController { >> %filename%
echo     public function __construct($db) { >> %filename%
echo         parent::__construct($db, ['#']);  >> %filename%
echo     } >> %filename%
echo. >> %filename%
echo } >> %filename%
echo ^?> >> %filename%

echo %classname%Controller.php has been created.
pause
