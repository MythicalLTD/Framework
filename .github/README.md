# Framework

The core framework for our projects ;)

For devs that want to modify the frontend using tailwind css make sure you build the frontend first :)

```bash
# Clean old build files
composer run frontend:clear
# Download the dependency's (Requires NodeJS)
composer run frontend:install:dependency
# Build the new frontend
composer run frontend:build

# Or you can also use watch to watch the changes live!
composer run frontend:watch
```
