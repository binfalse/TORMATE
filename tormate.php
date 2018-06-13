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


// load configuration
require_once ("tormate-config.php");

// returns true if url is supported.
// we will not deliver from localhost...
function check_url ($url) {
    // proper url?
    if (preg_match ('%^https?://([^/]+)(/.*)?$%i', $url, $matches)) {
        // not localhost
        if (stripos ($matches[1], "localhost") === false && stripos ($matches[1], "127.") === false)
            return true;
    }
    return false;
}


// check if cache dir is available, otherwise try to prepare it
if (!is_dir (CACHE_DIR) && !mkdir (CACHE_DIR)) {
    http_response_code (500);
    die ("error creating cache dir...");
}

$cachefile = CACHE_DIR . "/cache";
$cachedata = array ();

// is there already some cache?
if (is_file ($cachefile))
    $cachedata = json_decode (file_get_contents ($cachefile), true);


// check if there are old things that can be deleted
if (sizeof ($cachedata) >= MAX_CACHE) {
    foreach ($cachedata as $site => $prefs) {
        $cachedversion = CACHE_DIR . "/" . $site;
        if ($prefs["time"] + MAX_TIME < time () || !is_file ($cachedversion)) {
            unset ($cachedata[$site]);
            // also remove file if it exists
            if (is_file ($cachedversion))
                unlink ($cachedversion);
        }
    }
}


// did the client provide a url?
if (!isset ($_GET['url']) || empty ($_GET['url'])) {
    http_response_code (400);
    die ("no url provided");
}

// grab the sanitised url
$url = filter_var (trim ($_GET['url']), FILTER_SANITIZE_URL);

// is this a proper url?
if ($url && check_url ($url)) {

    // the hash identifies the url (-> will be used as filename and id in $cachedata)
    $hash = hash ("sha256", gethostname () . $url);
    // here we'll store the data
    $cachedversion = CACHE_DIR . "/" . $hash;

    // download the page if we haven't already
    if (!isset ($cachedata[$hash]) || !is_file ($cachedversion)) {
        // cache limit reached?
        if (sizeof ($cachedata) >= MAX_CACHE) {
            http_response_code (429);
            die ("cache limit reached");
        }

        // config for the new web page
        $prefs = array ("time" => time (), "header" => array ());

        // download the page
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_USERAGENT, "tormate");
        // regularly check progress to limit file size
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($download_size, $downloaded, $upload_size, $uploaded){
            // stop connection if download exceeds max file size
            if ($downloaded > (MAX_FILE_SIZE)) {
                http_response_code (400);
                die ("requested file is too large");
            }
        });

        // run curl
        $response = curl_exec($ch);

        // get the header
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);

        // get some information about the download and store it in $prefs[
        $prefs["responsecode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerlines = explode(PHP_EOL, $header);
        foreach ($headerlines as $line) {
            if (stripos ($line, "content-type") === 0)
                $prefs["header"][] = $line;
            elseif (stripos ($line, "etag") === 0)
                $prefs["header"][] = $line;
            elseif (stripos ($line, "content-disposition") === 0)
                $prefs["header"][] = $line;
        }

        // add everything to $cachedata and store it in the cache directory
        $cachedata[$hash] = $prefs;
        file_put_contents ($cachefile, json_encode ($cachedata));

        // save the downloaded data
        file_put_contents ($cachedversion,  substr($response, $header_size));
    }

    // arriving here, we already had the file in cache or just downloaded it
    // get the information about the file and prepare the response header
    $prefs = $cachedata[$hash];
    http_response_code ($prefs["responsecode"]);
    foreach ($prefs["header"] as $header) {
        header ($header);
    }

    // dump the file
    readfile ($cachedversion);
} else {
    // ups... the client provided an unsupported url..
    http_response_code (400);
    die ("this url is not supported..");
}



?>
