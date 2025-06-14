This guide will be mostly a mix of the original one, and some things i added. (original by SrCookie450 on GitHub, changed and site fixed by harryzawg)

-- THINGS YOU NEED --

node.js: ```https://nodejs.org/dist/v18.16.1/node-v18.16.1-x64.msi``` -- To run the renderer/build panel

postgreSQL: ```https://sbp.enterprisedb.com/getfile.jsp?fileid=1258627``` -- For the database

Dotnet 6: ```https://dotnet.microsoft.com/en-us/download/dotnet/thank-you/sdk-6.0.412-windows-x64-installer``` - To run the site

GO: ```https://go.dev/dl/go1.20.6.windows-amd64.msi``` - for Asset Validation

-- SETTING UP --

open CMD and use CD to go into the ```services/api``` folder and paste this ```npm i``` (installs node modules)

-- IMPORTANT --

Then, open CMD and use CD to go into your PostgreSQL folder. It should be at ```C:\Program Files\PostgreSQL\(your postgres version, if you followed the guide it will be 13)\bin```
Then copy the schema.sql file in ``services/api/sql``` to that PostgreSQL bin folder, then run
```psql --username=yourusername --dbname=yourdatabase < schema.sql```

Now, cd into ```services/Roblox/Roblox.Website```, rename the ```appsettings.example.json``` file to just ```appsettings.json```, then open it.

Change the default POSTGRES line that looks like this:
 ```"Postgres": "Host=127.0.0.1; Database=bubbabloxnew; Password=test; Username=postgres; Maximum Pool Size=20",``` 
and change that to:

``` "Postgres": "Host=127.0.0.1; Database=The database you want to use, if you want to use the default one, make this 'postgres'; Password=your Postgres password you set in the setup; Username=postgres; Maximum Pool Size=20",```

Now press ```ctrl + h``` and change C:\\Users\\Admin\\Desktop\\Revival\\ecsr\\ecsrev-main\\services\\ to C:\\whereever your ECS folder is\\services\\```

-- Site is mostly set up --

Go into ```services/api```, 
and create a folder named ```storage```.
Inside the storage folder make a folder named ```asset``` 
then go to ```services/api/public/images``` make a folder named ```thumbnails``` and ```group```

open CMD and use CD to go to ```services/admin```, then run ```npm i``` and ```npm run build```

Go to ```services/2016-roblox-main``` and rename the file named config.example.json to config.json.

Replace ```your.domain``` with your actual domain inside of that config.json file.

Now in a CMD window use CD to go into that same folder (```2016-roblox-main```), do ```npm i``` and then ```npm run build```.

Now go to ```services/renderer``` and rename the file named ```config.example.json``` to ```config.json``` and change it like this:
```
{
    "rcc": "C:\\where ever your ECS folder is, change this!\\services\\RCCService",
    "rccexe": "if you have your RCC named something else, change this to like RCC.exe or Server.exe, but it's probably named RCCService.exe so just change it to that",
    "authorization": "YourAUTH",
    "baseUrl": "https://yourdomain",
    "rccPort": 64989,
    "port": 3040,
    "websiteBotAuth": "YourBOTAUTH",
    "thumbnailWebsocketPort": 3189,
    "webhook": "changetoyourwebhook"
}
```
-- Replacing URLS --

Go into ```services/renderer/scripts```

Then go to the scripts folder, go into the ```player``` folder and in each file, replace "http/https://bb.zawg.ca" with your domain. (replace both HTTP and HTTPS)
Then in the ```asset``` folder's files, do the same thing that you did. Make sure to not replace the ```http``` and ```https```, so if something is HTTP keep it that way

Now, go back into the renderer and run ```npm i```, then ```npm run build```

-- DISCORD --

Replace ```https://bb.zawg.ca/discordcb``` in ```/services/api/public/Data/index.html``` 
with ```https://your.domain/discordcb``` (change your.domain to your actual domain)

Go to the Discord Developer Portal (```https://discord.com/developers/applications```) and make a new application.
Then go into OAuth2, and replace the client id in the same line that https://bb.zawg.ca/discordcb used to be in with your client ID.

Then add your redirect URL under the client ID section to be ```https://your.domain/discordcb```, replacing your.domain with your domain.

You should also update the client ID, secret and redirect URL in ```appsettings.json``` or else it would not work.

-- Almost there! --

Now, you should download [HxD](https://mh-nexus.de/en/downloads.php?product=HxD20) and drag the RCCService.exe file into that. Make sure your domain is exactly 10 characters, or it would not work correctly.
The reason being the way RCC was compiled it was set to use Roblox's domain which is 10 characters, so just replace it with your 10 char domain (CTRL + R, then do bb.zawg.ca then replace it with your domain. make sure your direction is all)
Do the same for the client. I would also recommend changing your public key (in Roblox/Roblox.Website/PublicKey and your private key, everything related to it. You can easily find guides/tools for it.)
Also, change the domain in AppSettings.xml to your domain. (for client and RCC)

-- IF YOU DO NOT HAVE A DOMAIN, OR A DOMAIN WITH 10 CHARACTERS --

If you don't, go into ```C:\Windows\System32\drivers\etc\hosts``` in notepad (RUN IT AS ADMIN, OR YOU CANNOT EDIT IT)

Then once in the file in an administrator Notepad, put something like 

```127.0.0.1 your10chardomain, can be anything but must be 10 characters```

then patch the RCC with your 10 character domain.

The site should be setup! Go into ```/services```, then run ```runall.bat``` and then when it's all done go to your site at localhost:90.
Sign up your account with the name ROBLOX (or whatever name), then go to https://your.domain/admin, and go to create player under Users, put ID 2500, the name as UGC and a random password, then go to that user on the admin panel and click nullify password.

Then, go back to Create Player and set the ID to 12, and the name as BadDecisions. Make sure it's a hard password.

-- Webserver --

You should change the Directory root in ```webserver\apache\conf\extra\httpd-vhosts.conf``` to your actual webserver root location.

You should also update everything in ```webserver\apache\conf\httpd.conf``` to your actual server root and directory locations.

Then you should be able to start the webserver, and connect using the client.

Now sign up normally.

congrats site made

-- Client instructions --

Start the webserver

Patch the client in HXD, the same way as RCC, then go to /game/get-join-script?placeid=(the place you want to join)

then go to the client's directory in CMD using CD, then do CLIENTNAME.exe (paste everything in the get join script endpoint after the client exe)

- Made by Deadly & harryzawg
