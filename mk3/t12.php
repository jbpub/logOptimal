<?php
 error_reporting(E_ALL);
shell_exec('setsid nohup php /var/www/mk3/cgi/qvmc ssreset &> /tmp/zz');
