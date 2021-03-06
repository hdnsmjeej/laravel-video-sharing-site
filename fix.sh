#!/bin/bash
# Copyright (c) 2016 Jacob Martin
# MIT license

# Fixes the permissions of the application for secure serving.
# Note that "www-data" is used here; feel free to change this for your own
# web server's username.

# If you don't have permissions to modify files and folders in the first place,
# this script will not work for you until you fix that with:
# sudo chown $USER -R .

function allowWrite() {
	find $1 -type f -exec chmod 664 {} +
	find $1 -type d -exec chmod 775 {} +
	chown $USER:www-data -R $1
}

sudo chown $USER:$USER -R .
find . -not -path './node_modules/*' -not -path './vendor/*' -type f -exec chmod 644 {} +
find . -not -path './node_modules/*' -not -path './vendor/*' -type d -exec chmod 755 {} +

allowWrite "storage"
allowWrite "bootstrap/cache"

chmod +x fix.sh
chmod +x artisan

# Give Selenium access to the databases.
chmod 775 database
chmod 664 database/database.sqlite
chown $USER:www-data database
chown $USER:www-data database/database.sqlite
