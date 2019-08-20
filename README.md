# Nottingham.Digital

Nottingham Digital events retrieval from:

* [Meetup.com](http://www.meetup.com/)
* [Ti.to](https://ti.to/)


## OAuth2 Authorisation for Meetup.com

Config requirements:

```
    'consumer_key'    => THE-CONSUMER-KEY,
    'consumer_secret' => THE-CONSUMER-SECRET,
    'redirect_uri' => 'https://URL/index.php?group=PHPMinds',
```

Requires `$_GET['meetup'] === 'meetup'` for the initial authorisation.
Use as:
```
/index.php?group=PHPMinds&authorise=meetup
```

> Note: Initial authorisation requires a manual opt-in through Meetup.com

Default `TokenProvider` uses `.token` file to save and access `token`.

Built for [Nottingham.Digital](http://nottingham.digital/)


