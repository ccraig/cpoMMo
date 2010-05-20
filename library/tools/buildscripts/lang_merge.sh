#!/bin/sh

####
###  After generating a fresh .pot file, merges its changes into existing translation files
#

cd poMMo

for i in `find language/ -name *.po`; do
	msgmerge -U $i ../pommo.new.pot
done
