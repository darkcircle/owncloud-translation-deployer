owncloud-translation-deployer
=============================
This is ownCloud translation deployer for who wants to apply translation immediately. This script only be able to run onto Unix like operating system. I will not support Windows server, but you can freely fork this source.
ownCloud translation workflow is controlled by Transifex translation deploy robot. but There is no script make be able to apply user translated translation immediately, so I write totally simple-_-code to convert po file to the php script that has translation string array. ough.. anyway, enjoy use it ;) and tell me if you think that it is suckx :-P lol

DISCLAIMER
==========
This script will be ineffective when ownCloud source tree structure has changed. 

Prerequisition
==============
You just should check php has installed into your system

   # whereis php
   /usr/bin/php

? yes. and your system should have an ownCloud.

Installation
============
Clone this source into the root of owncloud source tree

   /www/owncloud.foo.baz # git clone git://github.com/darkcircle/owncloud-translation-deployer
  
Set your favorite locale code. see also directory list of /ownCloudRoot/l10n/

   /www/owncloud.foo.baz # cd owncloud-translation-deployer/OCTransDeployer/config;vi locale.ini
 
Run
===
just execute like this.

   /www/owncloud.foo.baz/owncloud-translation-deployer # ./run.sh

and you can optionally use sudo command if you're logged in as a normal user.

   /www/owncloud.foo.baz/owncloud-translation-deployer $ sudo ./run.sh
