<div align="center">
    <p>
      <h1>BubbaBlox</h1>
    </p>
</div>

This is the source of Bubbablox. Credits below:

(original guide by <a href="https://github.com/SrCookie450">SrCookie450</a>, site fixed by <a href="https://github.com/harryzawg">harryzawg</a>)

## things you need
> **make sure to use specifically these versions or else site no workie**
- <a href="https://nodejs.org/dist/v18.16.1/node-v18.16.1-x64.msi">Node.js</a>, *to run the renderer/build panel*
- <a href="https://sbp.enterprisedb.com/getfile.jsp?fileid=1258627">PostgreSQL</a>, *for the database*
- <a href="https://builds.dotnet.microsoft.com/dotnet/Sdk/6.0.412/dotnet-sdk-6.0.412-win-x64.exe">.NET 6.0</a>, *to run the website*
- <a href="https://go.dev/dl/go1.20.6.windows-amd64.msi">Go</a>, *for asset validation*

## requirements
- at least Windows 10, Linux is untested as my server is a Windows machine. You should use Wine to run everything if you are using linux
- a 10 character long domain with SSL for HTTPS
- knowledge on how things like this work (you should have at least some experience with websites and coding to be able to host this. it's really not hard to set up if you know what you're doing.)

## database

**Note: ```npm i``` and ```npx knex migrate:latest``` won't work since there are no migrations and there is no package.json**

- open Command Prompt, and cd into your PostgreSQL folder. it should be at ```C:\Program Files\PostgreSQL\(your postgres version, if you followed the guide it will be 13)\bin```
- copy the schema.sql file in ```api/sql``` to that PostgreSQL bin folder, then run in a Command Prompt window in that folder:

```psql --username=** --dbname=* < schema.sql```

- `*` = the name of the database you want to use, if this is your first time installing, use postgres
- `**` = your postgres username, default is postgres if you didn't set any in the setup

## setting up

- rename the ```appsettings.example.json``` file in ```Roblox/Roblox.Website``` to ```appsettings.json```, then open it.
- also rename the ```game-servers.example.json``` to ```game-servers.json```. do not edit anything inside of it.
- make sure to edit the postgres line like this:
- ```"Postgres": "Host=127.0.0.1; Database=*; Password=your postgres password; Username=**; Maximum Pool Size=20",```
keep in mind:
- ``*`` = the name of the database you want to use, if this is your first time installing, use postgres
- ``**`` = your postgres username, default is postgres if you didn't set any in the setup

- press ```CTRL + H``` and change ```C:\\Users\\Admin\\Desktop\\Revival\\ecsr\\ecsrev-main\\services\\``` to ```C:\\whereever your folder is\\``` (make sure it's double slashed! so it should look like ```C:\\folder1\\folder2\\```)
- you should update everything in the appsettings.json file to your configuration.
- go to ```services/renderer```, rename the file named ```config.example.json``` to ```config.json``` and change everything inside of it so it works with your main site and matches your appsettings.json.
- **you must change GameServerAuthorization and the Authorization under Render in your appsettings.json to the Authorization in your renderer config.json.**

## thumbnails and frontend

- go into ```services/api```, 
- create a folder named ```storage```.
- inside the ```storage``` folder, make a folder named ```asset``` 
- go to ```services/api/public/images``` make a folder named ```thumbnails``` and ```groups```
- open Command Prompt and cd into ```services/admin```, then run ```npm i``` and ```npm run build```
- go to ```services/2016-roblox-main``` and rename the file named config.example.json to config.json.
- replace ```your.domain``` with your actual domain inside of that config.json file.
- in a Command Prompt window, cd into that same folder (```2016-roblox-main```), do ```npm i``` and then ```npm run build```
- also in a Command Prompt window, cd into ```renderer```, and do ```npm i``` and then ```npm run build```

## discord

- go to the <a href="https://discord.com/developers/applications">Discord Developer Portal</a> and make a new application.
- go into OAuth2, and replace the client id in the appsettings.json with your new client ID and client secret.
- add your redirect URL under the client ID section to be ```https://your.domain/discordcb```, replacing your.domain with your domain. do the same for ```https://your.domain/forgotcb```, and ```https://your.domain/logincb```.
- update the client ID, secret and add your new redirect URLs that you just added in the portal to ```appsettings.json``` or else it won't work.

## almost done!
i dont think you need to do the ns patch since its already compiled as roblox.com's domain. search for ns1 for good measure and make sure direction is all, then verify it's roblox.com!
- download [HxD](https://mh-nexus.de/en/downloads.php?product=HxD20) and drag the RCCService.exe file into it. make sure the domain you are using for this is exactly 10 characters, or it won't work correctly without a workaround (provided below).
- the reason for this is the way that RCC was compiled, it was set to use Roblox's domain which is 10 characters. just replace it with your 10 char domain (CTRL + R, then do bb.zawg.ca then replace it with your domain. make sure your direction is all)
- the only thing, is that you should search for ```NS1``` after closing the replace window and pressing ctrl + F and replacing your domain/bb.zawg.ca with roblox.com for each ns. so replace your.domain/bb.zawg.ca in ns1, with roblox.com and so on until ns3. [example](https://zawg.ca/assets/photos/demo1.png)
- also, change the domain in AppSettings.xml to your domain.

## the site should be setup at this point!
- go into ```/services```, run ```runall.bat```, when it's all done go to your site at your domain.
- sign up for an account with the name ```ROBLOX```, then go to /admin, and go to create player under Users, put ID 2500, the name as ```UGC``` and a random password, then go to that user on the admin panel and click Nullify Password.
- go back to Create Player and set the ID to 12, and the name as ```BadDecisions```, have common sense when making the password for this account.
- now, sign up with your account normally.

**congrats, site is setup and made!**

## webserver
note: this is important if you wanna upload assets and clients and stuff like that. ill soon put a tutorial on how to change the public key and priv key so it doesn't get mixed up with bubbablox's one!
- change the directory root in ```webserver\apache\conf\extra\httpd-vhosts.conf``` to your actual webserver root location.
- update everything in ```webserver\apache\conf\httpd.conf``` to your actual server root and directory locations.
- go into ```webserver/root/game``` then go into join.ashx and change the bs.zawg.ca and sitetest.zawg.ca URL's to your website URL. so sitetest/bs.zawg.ca should just look like your domain. go through every file and change it.
- do the same for PlaceLauncher.ashx and the asset endpoints, so it can actually get assets from your site in game.
- you should now be able to start the webserver, (```webserver\apache\bin\httpd.exe```) and connect using the client. (the webserver is required for Roblox assets as well, so make sure to start it!!)

## client (please only follow this if you wanna use a client)
- as the RCC, patch it without the ns patch and also change the baseUrl from ```AppSettings.xml``` to your domain. (make sure to change bb.zawg.ca and nothing else!)
- after patching the client, make sure to change your public and private keys. i dont specifically know where to change them but you can join the orc guide server. they have tons of guides on clients
- make sure to start the webserver
- go to /game/get-join-script?placeid=(the placeid of the place you want to join)
- then go to the client's directory in CMD using CD, then do `THE ACTUAL CLIENT NAME.exe` (usually Bubbablox.exe if you're tryna use their client, make sure to change the keys) (paste everything in the get join script endpoint after the client exe)
