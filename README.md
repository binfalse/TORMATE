# TORMATE is Tor's mate

Tor is a tool for annonymous communication over the internet.
However, there are a number websites that block traffic from the Tor network (or require solving a captcha, which cannot be done by your RSS reader etc..).
And that's why Tor needs a mate: TORMATE :)

TORMATE helps you fight against anti-anonymity.




## Idea and Usage

You can think of TORMATE as a gate into the web of Tor-blockers.
TORMATE can deliver resources from the legacy web, that would otherwise be inaccessible from the Tor network.
Just submit the URL of the desired content throught a [GET](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods) parameter and TORMATE will download the resource for you.
For example, to get the content of [https://binfalse.de](https://binfalse.de), you would call the following URL (assuming that you installed TORMATE at `https://tormate.url/tormate.php`):

    https://tormate.url/tormate.php?url=http://binfalse.de 

<small>(please note that `https://binfalse.de` is also available from Tor without the need for TORMATE)</small>

TORMATE will then check if it already cached the page behind `http://binfalse.de`.
If it's not in cache, it will download the page and store it in cache.
Anyway, it will forward the content to your client application - including a forwarded [status code](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes), `content-type`, `etag`, and `content-disposition` if available.
Cache settings and quotas can be configured.

Thus, in my RSS reader I can now add a feed of a Cloudflare (or similar) website, eg:


    https://tormate.url/tormate.php?url=https://contao.org/share/news-de.xml



## Setup

All you need to do is putting the files [tormate.php](tormate.php) and [tormate-config.php](tormate-config.php) into the same directory on your webserver.
You should then be able to access `http://your.server/dir/tormate.php`, and TORMATE will show some help/usage messages.


### Configuration

The configuration can be done in the `tormate-config.php` file.
TORMATE provides the following settings:

* `CACHE_DIR` points to a directory that TORMATE can use to cache pages.
* `MAX_TIME` is the time before a cached page becomes invalid.
* `MAX_CACHE` is the maximum number of pages that can be cached. If this limit is reached, TORMATE will refuse to download new pages.
* `MAX_FILE_SIZE` limits the file size that can be retrieved through TORMATE.
* `PROXY` can be used to tunnel TORMATE's traffic through another proxy.

All limits can also be disabled.
However, I recommend choosing proper values for `MAX_TIME` and `MAX_CACHE`, as they will prevent abuse.
For example, with a maximum of 100 files per hour (default), TORMATE is rather useless for [DoS attacks](https://en.wikipedia.org/wiki/Denial-of-service_attack) etc.
In addition, limiting the file size may prevent downloading videos etc.

If you for example know, that you just need TORMATE for five resources and change less than once a day anyway, you may want to set `MAX_TIME` to 60\*60\*24 (24 hours) and `MAX_CACHE` to 5.


### Dockerised Installation

TORMATE is also available as a Docker image at [binfalse/tormate](https://hub.docker.com/r/binfalse/tormate/).
The image has the TORMATE tool installed as `index.php`, so you just need to connect to the root of the webserver (without any `/tormate.php` or something).
The application is deployed using default settings.
If you want to change TORMATE's configuration, you just need to mount your configuration file over the default configuration.
To create a new configuration file, you can get guidance by the [tormate-config.php](tormate-config.php) file in this repository.
In short, you would probaly start TORMATE as this:

    docker run -d -v /your/config.php:/var/www/html/tormate-config.php -p 80:80 binfalse/tormate

Navigate your web browser to that machine and you should see some message from TORMATE.





## Discussion


Sure, TORMATE is just a workaround and doesn't solve the problem with nasty Tor-blockers.
A better solution would be to contact the owners of a website, explain the situation, and try to convince them to unblock Tor users.
However, some still *fear* the *bad* traffic, which they say comes from Tor...!?  
Consequently, you should avoid these Tor-blockers.
Unfortunately, there are situations that still force you into requiring the content (e.g. at work).
Typically, you would need to disable Tor for these Websites, but now there is TORMATE to help you getting access, without extending other software to implement exceptions or something!

**Do not install TORMATE in sensitive networks!**
Otherwise, people may get access to machines, which aren't accessible by default.
For example, an attacker could call `https://tormate.url/tormate.php?url=https://hidden.machine.org/secret.html` and TORMATE will deliver the `secret.html` file, while the attacker regularly does not have access to `hidden.machine.org` (e.g. because of a firewall in between attacker and `hidden.machine.org`).
Thus, TORMATE may accidentally help people to bypass firewalls...


## Licence

    TORMATE - Tor's mate
    Copyright (C) 2018 Martin Scharm <https://binfalse.de/contact/>
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
      
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.





