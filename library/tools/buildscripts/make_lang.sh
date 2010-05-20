#!/bin/sh

cd /maxtor/work/eclipse
echo "" > pommo.new.pot
./tsmarty2c.php poMMo/themes > pommo_theme_lang.php
xgettext -j -c -d pommo.po -o pommo.new.pot --keyword=_T --keyword=_TP -F pommo_theme_lang.php
find poMMo/ -iname "*.php" -exec xgettext -j -d pommo.po -o pommo.new.pot --keyword=_T --keyword=_TP {} \;
