## CWP search recipe

[![Build Status](https://travis-ci.org/silverstripe/cwp-recipe-search.svg?branch=master)](https://travis-ci.org/silverstripe/cwp-recipe-search)

This includes the following core SilverStripe and CWP modules:

 * [silverstripe/recipe-cms](https://github.com/silverstripe/recipe-cms): Recipe containing CMS, versioned, asset-admin, etc
 * [cwp/cwp-recipe-cms](https://github.com/silverstripe/cwp-recipe-cms): CWP CMS requirements recipe
 * [cwp/cwp-search](https://github.com/silverstripe/cwp-search): CWP fulltextsearch integration module
 * [silverstripe/fulltextsearch](https://github.com/silverstripe/silverstripe-fulltextsearch): Adds external full text
   search engine support to SilverStripe, specically with Solr in a CWP context
 * [symbiote/silverstripe-queuedjobs](https://github.com/symbiote/silverstripe-queuedjobs): A module that provides
   interfaces for scheduling jobs for certain times

You can also opt in to install the following suggested (not required) modules:

 * [silverstripe/textextraction](https://github.com/silverstripe/silverstripe-textextraction): Text Extraction API for
   SilverStripe CMS (mostly used with 'fulltextsearch' module)

This can be either added to an existing project or used as a project base for creating a basic CWP install.

## Get started

You can create a project using Composer:

```
composer create-project cwp/cwp-recipe-search ./myproject ^2
```

## More information

See the [recipe plugin](https://github.com/silverstripe/recipe-plugin) page for instructions on how
SilverStripe recipes work.
