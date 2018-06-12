<?php

/* where should we store cached pages?
 *
 * defaults to /tmp/tormate-cache/
 */
define ("CACHE_DIR", "/tmp/tormate-cache/");

/* how long (in seconds) should sites be cached? after this time is exceeded,
 * they get deleted from cache and we need to download them again
 *
 * defaults to 1 hour
 */
define ("MAX_TIME", 60*60);

/* how many websites should be cached per time?
 * if a website reaches MAX_TIME, it gets removed from the cache
 *
 * defaults to 100
 */
define ("MAX_CACHE", 100);

/* what is the maximum file size? we don't want to download movies or something...
 *
 * defaults to 5MB
 */
define ("MAX_FILE_SIZE", 5 * 1024 * 1024);


?>
