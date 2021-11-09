<?php

require('/var/www/html/private/config.php');
require('/var/www/html/private/init/mysql.php');
require('/var/www/html/private/init/var.php');
require('/var/www/html/private/func.php');

// Get updated releases in last 2 minutes
$releases = _getLatestUpdatesReleases(60);

// Make hook
foreach ($releases as $key => $release) {
    APIv2_UpdateTitle($release['id']);
}