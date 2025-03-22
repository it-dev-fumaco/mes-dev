@echo off
REM Set PHP version path
set PHP_PATH=C:/php/7.4/php.exe

REM Change directory to Laravel project root
cd /d %~dp0

REM Check if a port parameter is provided, otherwise use default (80)
set PORT=80
if not "%~1"=="" set PORT=%~1

REM Refresh cache
%PHP_PATH% artisan optimize:clear

REM Start PHP built-in server
%PHP_PATH% artisan serve --host=10.0.49.27 --port=%PORT%