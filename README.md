# ↓markdown↓ CMS README

A simple, fast, lean, flat file (no database) CMS for easy and quick website generation and publishing. If you can type text you can build a website.

## Requirements

A HTTP server (e.g. Apache) with PHP installed, that's all. Great for low cost hosting plans. A web server with PHP is supplied as standard by nearly all web hosting providers. ↓markdown↓ CMS is easy to configure for a Virtual Private Server (VPS), dedicated server or any PC. 

## Install

Drop [the code](http://tekeye.uk/downloads/markdown-cms.zip "↓markdown↓ CMS Zip File") on the web server. Just extract the zip contents to the public HTML root folder (if using the [GitHub zip file](https://github.com/GR8DAN/markdown-CMS/archive/master.zip "↓markdown↓ CMS from GitHub") extract everything in zip file's first folder). If PHP is enabled and the web server supports URL rewrite, as most do, then ↓markdown↓ CMS is ready (e.g. example.com/md).

## Add Files

Folder and files are structure and content. Start by creating an **index.md** file in the root. Add **.md** files for more web pages:

* _index.md_ for **example.com**
* _apage.md_ for **example.com/apage**
* _sub/page.md_ for **example.com/sub/page**

## Set Site Name and Logo

Upload an image for the website logo. Copy **md/md-config.php** to **site-config.php** (i.e. into the public root folder). Edit **site-config.php** to set the values for the site name and the logo.

## Further Information

* ↓markdown↓ CMS [Home Page](http://tekeye.uk/md/ "↓markdown↓ CMS Home Page")
* ↓markdown↓ CMS on [GitHub](https://github.com/GR8DAN/markdown-CMS "↓markdown↓ CMS on GitHub")
* Articles to [Build a Website from Scratch with ↓markdown↓ CMS](http://tekeye.uk/md_cms/build-a-website-from-scratch "Articles on Using ↓markdown↓ CMS")
* [License](/md/markdown-cms-license "↓markdown↓ CMS License")

