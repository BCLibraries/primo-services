`primo-services` are a PHP interface to Primo's X-Service Web services. It currently supports the *brief search* and *full view* services. It can also create Deep Links to Primo searches.

## Installation

`primo-services` uses the [Composer](http://getcomposer.org/) dependency management system. To install 

1. If you haven't already, [install `composer.phar`](http://getcomposer.org/doc/00-intro.md#installation-nix). To install `composer.phar` in the `/usr/bin` directory on Linux/OS X:
 
		sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin

2. Create a `composer.json` file. The example below will install `primo-services`:

		{
		"repositories": [
		    {
		        "type": "vcs",
		        "url": "https://github.com/BCLibraries/primo-services"
		    }
		],
		    "require": {
		            "bclibraries/primo-services" : "master"
		    },
		    "minimum-stability": "dev"
		}
    
3. Install using `composer.phar`:

		php composer.phar install
		
4. Instantiate:

         use \BCLib\PrimoServices\PrimoServices;
         use \BCLib\PrimoServices\Query;
         use \BCLib\PrimoServices\QueryTerm;
       
         require_once('vendor/autoload.php');
       
         $host = 'primo2.staging.hosted.exlibrisgroup.com'; //Your Primo host.
         $inst = 'BCL'; // Your Primo institution code.
         $primo = new PrimoServices($host, $inst);
   
## Use

### Brief search

First instatiate a connection to the Primo server:

```PHP
use \BCLib\PrimoServices\PrimoServices;
use \BCLib\PrimoServices\Query;
use \BCLib\PrimoServices\QueryTerm;

$host = 'primo2.staging.hosted.exlibrisgroup.com';
$inst = 'BCL';
$primo = new PrimoServices($host, $inst);
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

### Full view

Full views require a Primo record ID and return a single BibRecord:

```PHP
$result = $primo->request('ALMA-BC21421259580001021');
```

### Results

The result will be a *BriefSearchResult* object containing seach result and facet information. The BriefSearchResult object gives access to a list of *Facets*, a list of *BibRecords* for the retrieved full results, and a count of the total results of the query.

```PHP
$facets = $primo_result->facets;
$full_results = $primo_result->results;
$total = $primo_result->total_results;
```

#### Facets

To see a list of al facets, with facet values 

```PHP
echo "Facets\n";
foreach ($primo_result->facets as $facet) {
    echo "  Id:" . $facet->id . "\n";
    echo "    Count: " . $facet->count . "\n";

    echo "    Values:\n";
    foreach ($facet->values as $value) {
        echo "      " . $value->display_name . " (" . $facet->id . ") - " . $facet->count . "\n";
    }
}
```

#### Full results

Full results are stored in BibRecord objects. A BibRecord contains the following `string` and `string[]` components:

```PHP
foreach ($primo_result->results as $result) {

    // Accessible string and string [] properties of a result
    $result->id;
    $result->abstract;
    $result->collection_facet; // string[]
    $result->cover_images; // string[]
    $result->creator_facet; // string[]
    $result->date;
    $result->description; // string[]
    $result->display_subject;
    $result->format;
    $result->frbr_group_id;
    $result->fulltext;
    $result->genres; // string[]
    $result->isbn; // string []
    $result->issn; // string[]
    $result->languages; // string[]
    $result->link_to_source;
    $result->oclcid;    
    $result->openurl; // string[]
    $result->openurl_fulltext; // string[]
    $result->publisher;
    $result->reserves_info;
    $result->sort_creator;
    $result->sort_date;
    $result->sort_title;
    $result->subjects; // string[]
    $result->title;
    $result->type;
    $result->getit; // string[]
}
```

The creator is a *Person* object:

```PHP
$result->creator->display_name;
$result->creator->first_name;
$result->creator->last_name;
```

Each record is composed of *BibComponent* objects that indicate the source record used to create the Primo record. Most Primo records will have 1 component. De-duplicated records will have multiple components:

```PHP
foreach ($result->components as $component) {
    $component->alma_id;
    $component->delivery_category;
    $component->source;
    $component->source_record_id;
}
```

Each record also has an array of *GetIt* objects:
 
```PHP
foreach ($result->getit as $getit) {
    $component->getit1;
    $component->getit2;
    $component->category;
}
```

Other PNX fields can be retrieved using `field()`:

```PHP
foreach ($result->field('display/lds02') as $lds02) {
    echo "$lds02\n";
}
```

### Deep Links

You can also use the services to generate [Deep Links](http://www.exlibrisgroup.org/display/PrimoOI/Deep+Links):

```PHP
$primo_services = new BCLib\PrimoServices\PrimoServices('bc-primo.hosted.exlibrisgroup.com', 'BCL');
$query_term = new PrimoServices\QueryTerm();
$query_term->keyword('otters');

$deep_link = $primo_services->createDeepLink();
$deep_link->view('bclib')->onCampus('true')->group('GUEST')->language('eng');

echo $deep_link->search($query_term) . "\n";
echo $deep_link->link('ALMA-BC21421261320001021') . "\n";
```

### Caching

Results can be cached by injecting a [Doctrine cache object](http://docs.doctrine-project.org/en/2.0.x/reference/caching.html) when the service is initialized:

```PHP
use BCLib\PrimoServices\PrimoServices;
use Doctrine\Common\Cache\ApcCache;

$host = 'primo2.staging.hosted.exlibrisgroup.com';
$inst = 'BCL';
$cache = new ApcCache();
$primo = new PrimoServices($host, $inst, $cache);
```

## Testing

This module uses the Composer-installed PHPUnit. From the main project directory:

    ./vendor/bin/phpunit test/BCLib/
    
## License

See MIT-LICENSE


