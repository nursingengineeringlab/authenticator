# OpenID Connect Authenticator

This authenticator is designed to allow intranet services access to third-party
OpenID Connect authenticators.

The intranet application and this authenticator communicate using HTTP GET
methods and redirections.

## Setting up the authenticator

### Environment

The authenticator expects a PHP 7.x environment with cURL. You can set up a
reasonable environment under Debian or Ubuntu using:

```
apt install nginx php-fpm php-curl
```

The authenticator have no database requirement.

### Configuration file `config.php`

This repository does not contain the configuration file `config.php`. You create
the file from `config.example.php` and set up access tokens from various ID
providers.

## Calling the authenticator

The authenticator is called usign HTTP GET requests. In this section, we assume
the root of all API calls are based on `https://example.com/` and the app is
located at `https://app.local/`.

### Authenticating the user

To authenticate the user, you call the login endpoint with two parameters: the
user's Email address, and the application's identity string.

```
https://example.com/login.php?email=user@example.net&for=example-app
```

Parameters:

* `email`: The user's email address.
* `for`: The intranet app's identity string.

### Authentication Callback

Once the user is authenticated, the authenticator calls back to the intranet app
using HTTP GET. This callback differ between successful and failed external
authentication.

In the case of a successful authentication, the callback would look like the
following:

```
https://app.local/auth?email=user@example.net&provider=msft&id_token=ey...
```

Parameters:

* `email`: The authenticated user's email address.
* `provider`: The identity provider used for the user. This is useful in
  validating the token.
* `id_token`: JSON Web Token identifying the user. Signed with the provider's
  keys as per OpenID Connect required.

In the case of a failed authentication, the callback would look like this:

```
https://app.local/auth?error=not-an-email
```

Parameters:

* `error`: Reason for the authentication to fail. It can be:
	* `not-an-email`: The provided user ID is not an Email address.
	* `no-provider`: The identity provider could not be discovered.
	* `login-failed`: The identity provider logged the user in with the wrong
	  ID.
	* `500`: Something else went wrong.

## Contact

This code is written by Max Chan &lt;<beizongchen@umass.edu>&gt;.

&copy; 2021-2022 University of Massachusetts Amherst

