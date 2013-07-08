`primo-services` are a PHP interface to Primo's X-Service Web services. Access is available to the *brief search* and *full view* services.

## Installation

`primo-services` uses the [Composer](http://getcomposer.org/) dependency management system. To install 

1. If you haven't already, [install `composer.phar`](http://getcomposer.org/doc/00-intro.md#installation-nix). To install `composer.phar` in the `/usr/bin` directory on Linux/OS X:
 
		sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin

2. Create a `composer.json` file. The example below will install `primo-services`:


		{
		"repositories": [
		    {
		        "type": "vcs",
		        "url": "ssh:git@libdev.bc.edu:root/primo-services.git"
		    }
		],
		    "require": {
		            "bclibraries/primo-services" : "master"
		    },
		    "minimum-stability": "dev"
		}
    
3. Install using `composer.phar`:

		php composer.phar install
   
## Use

### Brief search

First instatiate a connection to the Primo server:

```PHP
use \BCLib\PrimoServices\PrimoServices;
use \BCLib\PrimoServices\Query;
use \BCLib\PrimoServices\QueryTerm;

$primo = new PrimoServices('primo2.staging.hosted.exlibrisgroup.com');
```

Searches are represented by a *Query*. Each Query can have one or more *QueryTerms*, which represent search parameters:

```PHP
$query = new Query('BCL');
$term = new QueryTerm();
$term->keyword($keyword);
$query->addTerm($term);
```

Finally execute the search:

```PHP
$primo_result = $primo->search($query);
```

### Results

The result will be a *BriefSearchResult* object containing seach result and facet information.