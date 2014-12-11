owncloud-translation-deployer
=============================
This is ownCloud translation deployer for who wants to apply translation immediately to the ownCloud. This script only be able to run onto Unix like operating system. I will not support Windows server, but you can freely fork this source.
ownCloud translation workflow is controlled by Transifex translation deploy robot. but There is no script make be able to apply user translated translation immediately, so I write totally simple-_-code to convert po file to the php script that has translation string array. ough.. anyway, enjoy use it ;) and tell me if you think that it is suckx :-P lol

DISCLAIMER
==========
This script will be ineffective when ownCloud source tree structure has changed. 

Prerequisition
==============
You just should check php has installed into your system

<pre># whereis php
/usr/bin/php</pre>

? yes. and your system should have an ownCloud.

Installation
============
Clone this source into the root of owncloud source tree

<code>/www/owncloud.foo.baz # git clone git://github.com/darkcircle/owncloud-translation-deployer</code>
  
Set your favorite locale code. see also directory list of /ownCloudRoot/l10n/

<code>/www/owncloud.foo.baz # cd owncloud-translation-deployer/OCTransDeployer/config;vi locale.ini</code>
 
Run
===
just execute like this.

<code>/www/owncloud.foo.baz/owncloud-translation-deployer # ./run.sh</code>

and you can optionally use sudo command if you're logged in as a normal user.

<code>/www/owncloud.foo.baz/owncloud-translation-deployer $ sudo ./run.sh</code>
