#!/bin/bash
cd ..
composer update
composer run tests

if [ $? -ne 0 ]; then
    echo "Error occurred during tests. Stopping the script."
    exit 1
fi

if [ -f "settings.json" ]; then
    mv settings.json production_settings.json
fi

if [ -f "migrated_files.txt" ]; then
    mv migrated_files.txt production_migrated_files.txt
fi
echo "{}" > settings.json
echo "" > migrated_files.txt
echo "This is the first install file ;)" > FIRST_INSTALL
