#!/bin/bash

wp-env run cli wp theme activate twentytwentythree
wp-env run cli wp rewrite structure /%postname%
wp-env run cli wp option update blogname "Ping Me On Slack"
wp-env run cli wp option update blogdescription "Get notifications on Slack when changes are made on your WP website."
