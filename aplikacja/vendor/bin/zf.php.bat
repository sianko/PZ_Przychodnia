@ECHO OFF
SET BIN_TARGET=%~dp0/../zendframework/zftool/zf.php
REM SET BIN_TARGET=C:\wamp\www\wirtualna_przychodnia\aplikacja\vendor\zendframework\zftool\zf.php

php "%BIN_TARGET%" %*
