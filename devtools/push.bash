#!/bin/bash
composer update
#composer run tests

if [ $? -ne 0 ]; then
    echo "Error occurred during tests. Stopping the script."
    exit 1
fi

if [ -f "settings.json" ]; then
    mv settings.json production_settings.json
fi


echo '{
    "__last_updated": "-",
    "framework": {
        "version": "1.0.1",
        "branch": "develop",
        "debug": "false",
        "name": "MythicalFramework"
    },
    "database": {
        "host": "127.0.0.1",
        "port": "3306",
        "username": "",
        "password": "",
        "name": "framework"
    },
    "encryption": {
        "method": "MythicalCore",
        "key": ""
    }
    }' > settings.json
echo "This is the first install file ;)" > FIRST_INSTALL
