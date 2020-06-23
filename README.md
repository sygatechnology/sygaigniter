# Syga CI Api Development

[![Coverage Status](https://coveralls.io/repos/github/sygatechnology/syga-ci-api/badge.svg?branch=develop)](https://coveralls.io/github/sygatechnology/syga-ci-api?branch=develop)
<br>

## Qu'est-ce que Syga CI Api?

Syga CI Api est un système de création d'API, développé à partir du framework web PHP CodeIgniter. 
Plus d'informations peuvent être trouvées sur le [site officiel] (http://codeigniter.com).

Ce référentiel contient uniquement le code source de CodeIgniter 4. (4.0.0-rc4)

**Il s'agit d'un code de pré-version et ne doit pas être utilisé sur les sites de production.**

Plus d'informations sur les plans de la version 4 de CodeIgniter peuvent être trouvées dans [l'annonce] (http://forum.codeigniter.com/thread-62615.html) sur les forums.

### Documentation

Le [Guide de l'utilisateur] (https://codeigniter4.github.io/userguide/) est la documentation principale de CodeIgniter 4.

The current **in-progress** User Guide can be found [here](https://codeigniter4.github.io/CodeIgniter4/). 
As with the rest of the framework, it is a work in progress, and will see changes over time to structure, explanations, etc.

You might also be interested in the [API documentation](https://codeigniter4.github.io/api/) for the framework components.

## Important Change with index.php

index.php is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!
The user guide updating and deployment is a bit awkward at the moment, but we are working on it!

## Repository Management

We use Github issues to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

If you raise an issue here that pertains to support or a feature request, it will
be closed! If you are not sure if you have found a bug, raise a thread on the forum first -
someone else may have encountered the same thing.

Before raising a new Github issue, please check that your bug hasn't already
been reported or fixed. 

We use pull requests (PRs) for CONTRIBUTIONS to the repository.
We are looking for contributions that address one of the reported bugs or
approved work packages.

Do not use a PR as a form of feature request.
Unsolicited contributions will only be considered if they fit nicely
into the framework roadmap.
Remember that some components that were part of CodeIgniter 3 are being moved
to optional packages, with their own repository.

## Contributing

We **are** accepting contributions from the community!

We will try to manage the process somewhat, by adding a ["help wanted" label](https://github.com/codeigniter4/CodeIgniter4/labels/help%20wanted) to those that we are 
specifically interested in at any point in time. Join the discussion for those issues and let us know 
if you want to take the lead on one of them.

At this time, we are not looking for out-of-scope contributions, only those that would be considered part of our controlled evolution!

Please read the [*Contributing to CodeIgniter*](https://github.com/codeigniter4/CodeIgniter4/blob/develop/CONTRIBUTING.md) section in the user guide.

## Server Requirements

PHP version 7.2 or higher is required, with the following extensions installed: 


- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- xml (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)

## Running CodeIgniter Tests

Information on running the CodeIgniter test suite can be found in the [README.md](tests/README.md) file in the tests directory.
