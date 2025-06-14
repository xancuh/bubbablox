This guide will be mostly a mix of the original one, and some things i added. (original by SrCookie450 on GitHub, changed and site fixed by harryzawg)

-- THINGS YOU NEED --

node.js: ```https://nodejs.org/dist/v18.16.1/node-v18.16.1-x64.msi``` -- To run the renderer/build panel

postgreSQL: ```https://sbp.enterprisedb.com/getfile.jsp?fileid=1258627``` -- For the database

Dotnet 6: ```https://dotnet.microsoft.com/en-us/download/dotnet/thank-you/sdk-6.0.412-windows-x64-installer``` - To run the site

GO: ```https://go.dev/dl/go1.20.6.windows-amd64.msi``` - for Asset Validation

-- SETTING UP --

Go to ```services/api``` and make a file named ```config.json``` and paste this code into it
```
{
    "knex": {
	"client": "pg",
        "connection": {
        "host": "127.0.0.1",
        "user": "postgres",
        "password": "Your POSTGRES password you set in the setup",
        "database": "The database you want to use, if you want to use the default one, make this 'postgres'"
        }
    }
}
```

Now, open CMD and CD into the ```services/api``` folder and paste this ```npm i``` (installs node modules)

-- IMPORTANT --

Then, do npx knex migrate:latest which installs the database tables and shit.
If successful and your DB data is correct, then it should say
"Batch 1 run: 1 migrations"

--	     --

Now, cd into ```services/Roblox/Roblox.Website```, rename the ```appsettings.example.json``` file to just ```appsettings.json```, then go into it.

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

Go to ```services/admin``` and run ```npm i``` and ```npm run build``` in cmd.

Go to ```services/2016-roblox-main``` and create a file named ```config.json```

Paste this into the config.json file:
```
{
  "serverRuntimeConfig": {
    "backend": {"csrfKey":"g0qiiDZw7jM2l54+7qsuRaymx6nBGdCKT9Kc0bqJB3aZ26rSsPMXfg8uWfUBtTqWenDVy+AQS1jkdrgvUwVSsw=="}
  },
  "publicRuntimeConfig": {
    "backend": {
      "proxyEnabled": true,
      "flags": {
        "myAccountPage2016Enabled": true,
        "catalogGenreFilterSupported": true,
        "catalogPageLimit": 28,
        "catalogSaleCountVisibleFromDetailsEndpoint": true,
        "commentsEndpointHasAreCommentsDisabledProp": true,
        "catalogDetailsPageResellerLimit": 10,
        "avatarPageInventoryLimit": 10,
        "friendsPageLimit": 25,
        "settingsPageThemeSelectorEnabled": true,
        "tradeWindowInventoryCollectibleLimit": 10,
        "moneyPagePromotionTabVisible": false,
        "gameGenreFilterSupported": true,
        "avatarPageOutfitCreatedAtAvailable": true,
        "catalogDetailsPageOwnersTabEnabled": true,
        "launchUsingEsURI": false
      },
      "baseUrl": "https://your.domain/",
      "apiFormat": "https://your.domain/apisite/{0}{1}"
    }
  }
}
```
You should replace your.domain with your actual domain.

Now in a cmd window in the same folder, do ```npm i``` and ```npm run build```

Now go to ```services/renderer``` and make a file named config.json.
```
{
    "rcc": "C:\\where ever your ECS folder is, change this!\\services\\RCCService",
    "rccexe": "if you have your RCC named something else, change this to like RCC.exe or Server.exe, but it's probably named RCCService.exe so just change it to that",
    "authorization": "THISISTHEAUTHFORRCCRAHHHHHHHHH",
    "baseUrl": "https://yourdomain",
    "rccPort": 64989,
    "port": 3040,
    "websiteBotAuth": "UW8U8TU9W9R8RHGRJOGWGOINOOWGNWRNJWWNRJ",
    "thumbnailWebsocketPort": 3189,
    "webhook": "changetoyourwebhook"
}
```
-- Replacing URLS --

Go into ```services/renderer/scripts```

Then go to the scripts folder, go into the ```player``` folder and in each file, replace "http/https://bb.zawg.ca" with your domain. (replace both HTTP and HTTPS)
Then in the ```asset``` folder's files, do the same thing that you did. Make sure to not replace the ```http``` and ```https```, so if something is HTTP keep it that way

Now, go back into the renderer and run ```npm i```, then ```npm run build```

-- Almost there! --

Now, you should download HXD and open the RCCService exe into that. Make sure your domain is exactly 10 characters, or it would not work correctly. (or you can just remove random 00's in the exe, but this sucks and is not recommended if your domain is shorter than 10)
The reason being the way RCC was compiled it was set to use Roblox's domain which is 10 characters, so just replace it with your 10 char domain (CTRL + R, then do bb.zawg.ca then replace it with your domain. make sure your direction is all)
Do the same for the client. I would also recommend changing your public key (in Roblox/Roblox.Website/PublicKey and your private key, everything related to it. You can easily find guides/tools for it.)
Also, change the domain in AppSettings.xml to your domain. (for client and RCC)

The site should be setup! Go into ```/services``` on the source then run ```runall.bat``` and then when its all done go to your site at localhost:5000, 
Sign up your account with the name ROBLOX (or whatever name), then go to https://your.domain/admin, and go to create player under Users, put ID 2500, the name as UGC and a random password, then go to that user on the admin panel and click nullify password.

Also, start the webserver. (if you want client)

Now, go back to Create Player and set the ID to 12, and the name as BadDecisions. Make sure it's a hard password.
Now sign up normally.

congrats site made

-- Client instructions --

Start the webserver

Change the URL to yours at /game/PlaceLauncherBT.ashx in BypassController.cs (from bs.zawg.ca or whatever it is to your 10 char domain)

Patch the client in HXD, the same way as RCC, then go to /game/get-join-script?placeid=(the place you want to join)

then go to the client's directory in CMD, then do CLIENTNAME.exe (paste everything in the get join script endpoint after the client exe)

- Made by Deadly & harryzawg
