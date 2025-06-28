@echo off
REM === Delete legacy/unused PHP files ===
del adduser.php
del assigncity.php
del channel_add.php
del checkuser.php
del create_master.php
del custom_404.html
del export.php
del index.php.bkup
del lcn_delete.php
del lcn_edit.php
del lcn_name_edit.php
del lcn_swap.php
del lcnedit_process.php
del logout.php
del mod_log.php
del modifyuser.php
del package_delete.php
del package_edit.php
del package_master.php
del process.php
del secure_page.php
del submit.php
del submit_data.php
del swap_process.php
del tdpd.php
del user_delete.php
del viewmodifyuser.php
REM === Process folder ===
del process\submit.php
del process\lcnedit_process.php
del process\swap_process.php
REM === Admin folder ===
del admin\submit.php
del admin\logout.php
del admin\login.php
REM === Server_func folder ===
del server_func\index.php
REM === Classes folder (if empty, will be removed below) ===
REM === Lib folder (if empty, will be removed below) ===
REM === Remove legacy includes if not referenced ===
del include\connect.php
del include\hex_maker.php
del include\log.php
del include\package_return.php
REM === Remove legacy styles if not referenced ===
REM (Uncomment if you confirm not used in new views)
del style\dbslt.css
del style\login.css
del style\main.css
del style\menustyle.css
del style\style.css

REM === Remove empty folders ===
for /d %%F in (*) do (
    dir /b "%%F" | findstr . >nul || rd "%%F"
)
for /d %%F in (process admin Classes lib server_func style) do (
    if exist "%%F" (
        dir /b "%%F" | findstr . >nul || rd "%%F"
    )
)
REM === Remove empty folders in subdirectories ===
for /r %%D in (*) do (
    if exist "%%D" (
        dir /b "%%D" | findstr . >nul || rd "%%D"
    )
)
echo Cleanup complete.
pause