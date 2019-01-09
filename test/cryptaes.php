<pre>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

var_dump(cryptAES('super test', 'sdsdf'));
var_dump(cryptAES('3wQ+ZRXPaj1O6K8B0nQrX6mF25rpefUdaIaV+csQVT04JBwZLjtO0KA6nTG9UAUDKe8y0MOgOzJ+zABxY311qF8DD2K3cvNYWd7sGnxM+T1XEzj2jP3NVhD5gU16nTYO', 'sdsdf', 'decrypt'));
