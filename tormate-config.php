<?php
/*
 * Copyright (C) 2018 Martin Scharm <https://binfalse.de/contact/>
 *
 * This file is part of TORMATE - Tor's mate.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */



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
