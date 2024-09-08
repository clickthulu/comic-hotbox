# clickthulu/comic-hotbox
## Installation Instructions

Installation of the comic hotbox can either be simple or can be complicated.  Both methods will be laid out here.

If you've done things like this before, you won't need this guide.  However, if you're not used to working with Free Open Source Software (FOSS) or Symfony projects, this will be new to you.  

I'm assuming that you're going to need step by step instructions if you're reading this file.  Let's take it by the numbers.

You will need the following to run this.
1) A webserver, such as apache or nginx
2) PHP > 8.1 running on the server
3) Composer

Once your prep work is done, there are 2 methods to get things started.  Running the setup.php file and answering its prompts, or doing all the steps by hand.

## Prep Work

### 1) Download the code

I know, it's obvious right?  So, first thing to do is download the code and put it in your web path.  I'm going to leave the configuration of your webserver up to you.

### 2) Create your Database

Seems obvious, right?  Get a mysql database setup with a user/password to access it.  

### 3) Make sure you have composer

You can get composer over at https://getcomposer.org

### 4) Configure your webserver

Again, seems obvious, right?  Get your webserver pointed at the hotbox server code.

## Run the setup.php 

setup.php makes some assumptions, such as your composer install is **composer** and not **composer.phar** and that it exists in your PATH.

### a) Run setup

Run the following command:

`php ./setup.php`


## Do it yourself

### a) Create your .env file

Okay, so in /path/app there needs to be a file named **.env**  There is a .env.dist file that will get you started.  Copy the .env.dist file to .env and edit it to insert your data.  

### b) Run composer install without scripts

From the app/ folder run the following command:

`composer install --no-scripts`

### c) Run the doctrine migrations

From the app/ folder run the following command:

`./bin/console docker:migrations:migrate`

This will update the database and set the standard settings

### d) Run composer install again.  This time with scripts

`composer install`

The scripts are important, however without the database, they will error out and fail.  Running them again after the database has been created cleans up outstanding issues.

### e) Create your Owner Account

Run the following command

`./bin/console hotbox:add-user [email address]  [password] Owner,Administrator`

This will generate a new account for you

## Finally

### 1) Login and invite people

That's it.  Head to your public URL and login.  You should be able to start up right away.
