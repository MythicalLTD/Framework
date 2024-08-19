#!/bin/bash

# Path to the bash directory
bash_dir="./bash"

# Iterate over all .bash files in the directory
for file in "$bash_dir"/*.bash; do
    # Check if the file is a regular file
    if [[ -f "$file" ]]; then
        # Execute the bash file
        bash "$file"
    fi
done