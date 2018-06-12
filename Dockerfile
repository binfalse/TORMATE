FROM php:apache
MAINTAINER martin scharm <https://binfalse.de/contact/>

# just add tormate resources
ADD tormate.php /var/www/html/index.php
ADD tormate-config.php /var/www/html/


