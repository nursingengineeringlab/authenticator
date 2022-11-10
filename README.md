# OpenID Connect Authenticator

This authenticator is designed to allow intranet services access to third-party OpenID Connect authenticators.

The intranet application and this authenticator communicate using HTTP GET methods and redirections.

## Setting up the authenticator

### Environment

The authenticator expects a PHP 7.x environment with cURL. You can set up a reasonable environment under Debian or Ubuntu using:

```
apt install nginx php-fpm php-curl
```

The authenticator have no database requirement.

### Configuration file `config.php`

This repository does not contain the configuration file `config.php`. You create the file from `config.example.php`.

## Calling the authenticator

## Contact

This code is written by Max Chan &lt;<beizongchen@umass.edu>&gt;.

&copy; 2021-2022 University of Massachusetts Amherst

