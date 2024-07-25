#!/bin/bash

# Set the directory to search for PHP files
directory="./"

# Initialize the line count
line_count=0

apt install bc -y >> /dev/null 2>&1

# Function to recursively count lines of code in PHP files
count_lines() {
    local dir="$1"
    local files=$(find "$dir" -type f -name "*.php" ! -path "*/node_modules/*" ! -path "*/vendor/*")

    for file in $files; do
        # Count the lines of code in the file and add it to the line count
        lines=$(wc -l < "$file")
        line_count=$((line_count + lines))
    done
}

# Call the function to count lines of code
count_lines "$directory"

# Format the line count
if (( line_count >= 1000000 )); then
    formatted_count=$(printf "%.2fM" "$(bc -l <<< "$line_count/1000000")")
elif (( line_count >= 1000 )); then
    formatted_count=$(printf "%.2fK" "$(bc -l <<< "$line_count/1000")")
else
    formatted_count=$line_count
fi

# Print the total line count
echo "Total lines of code: $formatted_count"
