#!/bin/bash

# Function to replace spaces and colons in filenames
rename_files() {
  for file in $(find . -type f | sort -r); do
    if [[ -f "$file" ]]; then
      # Replace spaces with underscores
      new_name="${file// /_}"
      # Replace colons with %3A
      new_name="${new_name//:/%3A}"
      # Rename the file
      if [[ "$file" != "$new_name" ]]; then
        mv "$file" "$new_name"
        echo "Renamed '$file' to '$new_name'"
      fi
    fi
  done
}

# Call the function
rename_files
