#!/bin/bash
cd ..

if [ -f "production_settings.json" ]; then
    rm settings.json
    mv production_settings.json settings.json
fi

if [ -f "production_migrated_files.txt" ]; then
    rm migrated_files.txt
    mv production_migrated_files.txt migrated_files.txt
fi

rm FIRST_INSTALL