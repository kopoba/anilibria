<?php
$cache = new Memcached();
$cache->addServer($conf['memcache'][0], $conf['memcache'][1]) or die('memcache not work');
