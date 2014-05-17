@ECHO OFF
REM  As of SDK 15.0 and Open Server 15.0, Sybase library names have
REM  changed from lib<name> to libsyb<name> to avoid name clashes
REM  with other libraries.
REM
REM  To allow pre-15.0 applications to continue to work with the
REM  renamed shared libraries, this script is provided to copy the
REM  new library names to the old ones in %SYBASE%\%SYBASE_OCS%\dll.
REM  Usage of the script:
REM 
REM	copylibs.bat { create / remove }
REM
REM  where 'create' copies the old-named files in $SYBASE/$SYBASE_OCS/dll
REM  and 'remove' can be used to delete these files again.

SET DLLDIR=%SYBASE%\%SYBASE_OCS%\dll

IF "%SYBASE%"=="" (
        ECHO %%SYBASE%% is not set.
        GOTO end
)

IF "%SYBASE_OCS%"=="" (
        ECHO %%SYBASE_OCS%% is not set.
        GOTO end
)

IF "%1"=="create" GOTO create
IF "%1"=="remove" GOTO remove
GOTO usage

:create
copy %DLLDIR%\libsybblk.dll %DLLDIR%\libblk.dll
copy %DLLDIR%\libsybcobct.dll %DLLDIR%\libcobct.dll
copy %DLLDIR%\libsybcs.dll %DLLDIR%\libcs.dll
copy %DLLDIR%\libsybct.dll %DLLDIR%\libct.dll
copy %DLLDIR%\libsybdb.dll %DLLDIR%\libdb.dll
copy %DLLDIR%\libsybsrv.dll %DLLDIR%\libsrv.dll
copy %DLLDIR%\libsybxadtm.dll %DLLDIR%\libxadtm.dll
GOTO end

:remove
del %DLLDIR%\libblk.dll
del %DLLDIR%\libcobct.dll
del %DLLDIR%\libcs.dll
del %DLLDIR%\libct.dll
del %DLLDIR%\libdb.dll
del %DLLDIR%\libsrv.dll
del %DLLDIR%\libxadtm.dll
GOTO end

:usage
ECHO Usage: %0 { create / remove }

:end
