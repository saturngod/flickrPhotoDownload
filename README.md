# Flickr Downloader

Put flickr download file to your localhost.

Get KEY and SECRECT at [https://www.flickr.com/services/apps/create/](https://www.flickr.com/services/apps/create/).

Open **index.php** and change `$callbackURL = "http://localhost/flickr_download/index.php";` and update key,secrect.

If your folder name is `flickr` , it will be like `$callbackURL = "http://localhost/flickr/index.php";`.

Open in browser `http://localhost/flickr_download/index.php`.

It will authorize oauth and after finish you will receive token and secrect.


Example:

```
O_TOKEN: 86157674079732196-70bdda496a9f908f
O_SECRECT: b2a74ac72785db78
``` 

Update `O_TOKEN` and `O_SECRECT` in `download.php`.

Afer that , run

```
php download.php
``` 

It will create download folder and download all photo in there.