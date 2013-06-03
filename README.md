check-requirement
=================

Sometimes you need to transfert your website from one server to another. Or maybe you use to have a production version of your site and often you transfert this version from production to a new dev environnement. Then you need to check if new server meets the requirements. 
That what this micro app is made for !

It works with both Browser and CLI display. But be aware of CLI mays output differents things due to the system user rights and configurations that are not the same for webserver and local user.


Screenshots
==================
![Screenshot](http://img4.hostingpics.net/pics/247689html.png)
![Screenshot](http://img4.hostingpics.net/pics/611831cli.png)



How To
==================

[Download the archive](https://github.com/SneakyBobito/check-requirement/archive/master.zip) and extract it or clone the repo.

Open index.php and you will see some example. It is pretty straightforward.

The code used is made with very basical php (no OOP) in order to be compliant with older php versions, in this way this micro app doesnt need a check requirement to check if php version for the checker ! :]

You can customize the template in checker/header.html, for example replace the \<h1> content by the name/logo of your application.

You can also edit the css directly embedded in the head ( Yuck ! I know.. but the micro app doenst intend to grow up).


Check and Share !
==================

Any pull request to add new checker is welcomed, or open issue if you are lazy !



