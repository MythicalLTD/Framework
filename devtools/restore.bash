#!/bin/bash

if [ -f "production_settings.json" ]; then
    rm settings.json
    mv production_settings.json settings.json
fi

rm FIRST_INSTALL