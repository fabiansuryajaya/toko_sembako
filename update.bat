@echo off
echo Memulai update database toko_sembako...
mysql -u root toko_sembako < update.sql
echo Update database selesai!
pause