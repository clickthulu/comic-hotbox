# clickthulu/comic-hotbox
## Installation Instructions

Installation of the comic hotbox is medium complicated.  If you've done things like this before, you won't need this guide.  However, if you're not used to working with Free Open Source Software (FOSS) or Symfony projects, this will be new to you.  
I'm assuming that you're going to need step by step instructions if you're reading this file.  Let's take it by the numbers.

You will need the following to run this.
1) A webserver, such as apache or nginx
2) PHP > 8.1 running on the server
3) Composer

## 1) Download the code

I know, it's obvious right?  So, first thing to do is download the code and put it in your web path.  I'm going to leave the configuration of your webserver up to you.  

## 2) Create your Database

This is a commonly forgotten about step.  You need to have a database and user ready for comic-hotbox.  If you don't have one ready to go, get that done now.

## 3) Create your .env file

Okay, so in /path/app there needs to be a file named **.env**  There is a .env.dist file that will get you started.  Copy the .env.dist file to .env and edit it to insert your data.  

## 4) Run composer install without scripts

From the app/ folder run the following command:

**composer install --no-scripts**

## 5) Run the doctrine migrations

From the app/ folder run the following command:

**./bin/console docker:migrations:migrate**

This will update the database and set the standard settings

## 6) Run composer install again.  This time with scripts

**composer install**

The scripts are important, however without the database, they will error out and fail.  Running them again after the database has been created cleans up outstanding issues.

## 7) Create your Owner Account

Run the following command

**./bin/console add:user**

You will receive a walkthrough on creating a new user

## 8) Add administrative roles to your user

**./bin/console add:admin**

You will receive a walkthrough on adding administrative roles to your user

## 9) Login and invite people

That's it.  Head to your public URL and login.  You should be able to start up right away.
