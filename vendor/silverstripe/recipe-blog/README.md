## Overview

Provides a quick baseline installation for a blog and some common supporting
features.

## Getting started

To start a new website with a blog installed;

```
composer create-project silverstripe/recipe-blog website ^1
```

Or to include a blog into your existing website:

```
composer require silverstripe/recipe-blog
```

## Included functionality

This recipe includes `silverstripe/blog` and it's dependencies as one might
expect, but also provides a set of optional functionality enabled by the
presence of:

* `silverstripe/widgets`
* `silverstripe/content-widget`
* `silverstrpe/spamprotection`
* `silverstripe/akismet`
* `silverstripe/comments`
* `silverstripe/comment-notifications`
* `colymba/gridfield-bulk-editing-tools`

And includes some configuration out of the box.
