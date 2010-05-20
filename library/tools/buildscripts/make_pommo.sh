#!/bin/sh
rm -rf pommo
cp -a poMMo pommo 
rm pommo/config.php
rm -rf pommo/.project/
rm pommo/.cvsignore
sudo rm -rf pommo/cache/pommo
sudo rm pommo/cache/maintenance.php
chmod 777 pommo/cache
rm pommo/ChangeLog*
tar -c -f pommo-version.tar --exclude=CVS --exclude=.project --exclude=.svn  pommo/
rm -rf pommo/
tar xvf pommo-version.tar
gzip pommo-version.tar
zip -r pommo-version.zip pommo/
rm -rf pommo/
