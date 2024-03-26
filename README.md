### King-of-Mota

A web-based game inspired by the tv-shows: Breaking Bad and Weeds.

Created by Stian Arnesen | 2015 - 2017


## My first website

I started making this game almost 10 years ago back in 2015 when i was 18 years old. It originally started as a way for me to learn PHP, Javascript, CSS and MySQL. 
The code is really messy and can be hard to follow. 
Suddenly i had more than 50 registered users (most of which was friends) and i realised that this was a project that i would like to keep working on.
The game was never finished, and i had a lot more planned for this game.
I stopped working on the game in late 2017 after people got bored and it was harder and harder to maintain and add new features. 
I wish i was using Github a long time ago. It would be amazing to look back on the first versions and see the progress i had made over the years.

# What is King of Mota?

Mota is a spanish slang for weed that i heard on the tv-show Weeds.
In this game you play as a drug manufacurer growing weed, mushrooms and other scary drugs that disrupts society. 
The goal here is to have the highest level at the end of each season. 
The game also features the 'Lab' where you can manufacture drugs like: Meth, cocain and ecstacy.
To produce items in the Lab you will need various ingredients like mushrooms, coca leaf and more.
There is a custom admin panel to add new items, change specs and also to look at the history of changed items. This was to make it easier to go back on changes.

So basically this is a more complicated version of cookie-clicker.


# Game features

* Inventory: You start with 3 spaces in your inventory and can upgrade it as you need. (As long as you can afford the upgrade)
* Shop: In the shop you are able to buy seeds for weed and mushrooms and some special plants that are only needed when you unlock the Lab. Each item has its own minimum required level. Each product has its own product-page where you can read more about the item and see how much profit you will gain (Money and EXP) and how long it takes to grow.
* Garden: This is where you plant your seeds baught from the store. You start with one space to grow your seeds and can as many new spaces as you can afford.
* Lab: Create even scarier drugs like meth and cocain. There are usually 3 different ingredients needed to cook each drug.
* Backpack: Your backpack is the main-Inventory that is used when buying/collecting items.
* Storage containers: When your backpack is full it might be cheaper to by a storage container to store more items instead of upgrading your backpack. These containers can store more items for a lower price.
* Customizable profile: Custom profile picture and header image. Awards will be shown here also.
* Posts: On every profile you can write posts.
	* Likes: See something you like? Like it! The owner of the post will be notified of your like. Click on the number of likes to show the users who have liked the post.
	* Comments: Each post can be commented on. The owner of the post will be notified.
* Awards: Administrator can give custom awards to people who go the extra mile. These awards will be shown on leaderboards as well as on the users profile.
* Notifications: There is a dropdown menu showing notifications like: Friend requests (New/accepted), new post on yout profile. Comments on posts made by you.
* Leaderboard: See how your rivals are doing.
* Friends: Add friends to feel less lonely.
* Upgrades:
	* More storage space for the backpack and storage containers
	* Garden:
		* Light: Faster growing.
		* Soil: More exp for each harvest.
		* Air Ventilation: More money back when selling items.
		* Garden: More space for your plants to grow.		
	* Inventory:
		* Backpack: More space for your backpack.
		* Storage container: Buy additional containers.
* Chat (REMOVED): The game had a working chat where you could send text messages to your friends, but was not something this game needed and made it more cluttered. This also took too much CPU and therefore was removed. The code for the chat can be found inside the /communication folder.
* PVP (NOT COMPLETE): In the PVP arena you could attack your rivals and potentially steal some of their money if you had the better upgrades and some luck to overcome the RNG. This was never fully implemented since there was a lot more that had to be done for this to be a part of the game.
* Crew: Join a crew or create your own one. This had no function other than the social aspect it created. (crew image and name was Customizable)

# Good to know: 
The code for this project is messy and very inconsitent various places like naming: Variables, functions, filenames and folders follow no predictable pattern and can be hard to navigate through.
There are a lot of unececarry assignments to variables that are never used.
As mentioned this game started as my first website and therefore i knew very little of how code should be written. I also had not planned for anyone else to read my code.
I think its emberassing to show this code since i have learned to much since 2017, but sadly this is one of the few PHP projects that i made sure to backup on multiple harddrives.

I have tried to remove code that was not written by me, but some unoriginal code might still be present.
I had previously only been programming in Java, Actionscript 2.0/3.0 and some algorithms in C++ before i started this project.
SQL injection and XSS is very much a problem. Some XSS protection is added in a few places.

I found it hard to get my head around the require/include properly since this differs a lot from Java and other languages and ended up with something like this for all files:
The first solution i found was to include the 'DOCUMENT_ROOT' before any includes.

The server responds with a lot of HTML which takes a lot of CPU power. Later in development i realised the power of JSON combined with Javascript to transfer the workload to the client.

There are some files, functions and classes that are never used. I probably wont remove these as the project is just a great thing to look back on.
I also did not remove older files that i saved in case i needed them. (index2.php, shop-copy.php, ...)

The files inside /pvp should contain some of the last code i wrote for this game.

```php
<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];

require($ROOT . "layout/inventory-item/InventoryItem.php");


// Going back one folder to reach connection.php - Terrible.
include_once("../connect/connection.php");
```
	
This was a really bad choice that made it a lot harder for me in the future. Both when adding more modules and especially when launching to new servers.