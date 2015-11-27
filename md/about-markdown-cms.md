/*
Title: About ↓markdown↓ CMS
Description: This is the About Page for ↓markdown↓ CMS.
Author: Daniel S. Fowler
Tags: markdown, cms, about, website, publishing
Published: 2015-06-17
Updated: 2015-11-21
*/

# About ↓markdown↓ the Simple Flat File CMS

↓markdown↓ is a lightweight Content Management System (CMS) designed to quickly and easy publish websites. It is written using the PHP:Hypertext Preprocessor and runs on a HTTP (web) server. ↓markdown↓ CMS provides a web publishing platform with content accessed via a web browser over a network, usually the Internet. ↓markdown↓ CMS assumes that the person or team publishing content understands how to create files and folders, edit text and produce images and other media. The web site structure mimics the file structure in which the content is stored. This supports remote and disconnected working via file synching. Being a no database flat file CMS means the web site can be kept in a cloud synched folder (e.g. Dropbox, Google Drive or OneDrive) for automatic backups. Plus tools such as Git can be used to manage synchronisation and version control if required. 

<small>↓markdown↓ CMS was inspired by [Pico CMS](http://picocms.org/ "Pico Website") and by [Singularity](http://christopher.su/2012/singularity-cms-single-php-file/ "Christopher Su's Singularity") (also partly inspired by Pico). It uses [Skeleton CSS](http://getskeleton.com/ "Skeleton's Web Site") as the basis of the style sheet and the [Parsedown](http://parsedown.org/ "Parsedown Home Page") PHP module.</small>

## ↓markdown↓ CMS is Fast and Uncomplicated

↓markdown↓ CMS does not use frameworks or complex libraries. It was designed to "keep it simple, stupid" ([KISS](https://en.wikipedia.org/wiki/KISS_principle "The KISS Principle")) as an alternative to the increasingly bloated and complex popular CMSs such as WordPress, Drupal, Joomla and DNN. They are great tools but have become over engineered for simple web sites with straightforward requirements. ↓markdown↓ is a back to basics approach. The small code size and flat file content storage means it is fast and lean. Pushing content is a easy as typing text, and that can be done on any device, anywhere.

## Create A Website Using Free Software

↓markdown↓ CMS is open source web publishing software. This means you have access to all the code and files and, if required, you can tweak, or get someone to tweak, the software for your own needs. ↓markdown↓ CMS is good for quickly creating informational web sites for any type of organisation. It is aimed at those wanting to concentrate on publishing the content and not having to mess around with HTML mark up, page configuration, databases and web site management.

## How to Make a Website with ↓markdown↓ CMS

The following paragraph is a quick overview of getting a ↓markdown↓ CMS website going. For detailed information or to overcome any problems see the articles listed in [Build a Website from Scratch with ↓markdown↓ CMS](http://tekeye.uk/md_cms/build-a-website-from-scratch "Articles on Using ↓markdown↓ CMS").

First you need a HTTP web server that is running PHP, which most hosting providers give you. You login to your web server and upload the [↓markdown↓ CMS zip](http://tekeye.uk/downloads/markdown-cms.zip "Download ↓markdown↓ CMS") file. Extract the contents of the zip file into the public web directory and your web site now runs and is ready to accept content. (If using the [GitHub](https://github.com/GR8DAN/markdown-CMS "↓markdown↓ CMS on GitHub") zip file extract everything in the *markdown-CMS-master* folder within the zip).

Upload a graphic file for the logo and create a text file in the root public folder (called **index.md**). Enter some content in this index file. Copy **md/md-config.php** to the public root folder as **site-config.php**. Edit **site-config.php** to set the values for the website name and website logo. Your web site is up and running. You can build upon this basic website and get it noticed by the search engines by adding some relevant articles. To learn more see the [online articles](http://tekeye.uk/md_cms/build-a-website-from-scratch "Articles on Using ↓markdown↓ CMS").