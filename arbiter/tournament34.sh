#!/usr/bin/bash

SAVE_DIR=~/Desktop/tournament34

rm -rf $SAVE_DIR
mkdir $SAVE_DIR

php arbiter/tournament34.php \
    --binary agent/doofus/build/doofus --name doofus0 \
    --binary agent/doofus/build/doofus --name doofus1 \
    --binary agent/doofus/build/doofus --name doofus2 \
    --binary agent/doofus/build/doofus --name doofus3 \
    --binary agent/doofus/build/doofus --name doofus4 \
    --binary agent/doofus/build/doofus --name doofus5 \
    --binary agent/doofus/build/doofus --name doofus6 \
    --save $SAVE_DIR \
    --save-inputs \
    --table-size 3 \
    --games 3
