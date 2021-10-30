# Nottingham.Digital

Nottingham Digital events retrieval from:

* [Meetup.com](http://www.meetup.com/)
* [Ti.to](https://ti.to/)


> Note: Version 2.x no longer supports OAuth2 for Meetup.com

## Serverless

There is a `serverless.yml` file for using with Bref and AWS.
Remove vendor folder and populate without development dependencies:
`composer install --prefer-dist --optimize-autoloader --no-dev`

Then as long as you have serverless and awscli configured, you can run (with your own profile):

`serverless deploy --aws-profile serverless-notts-digital`

Built for [Nottingham.Digital](http://nottingham.digital/)
