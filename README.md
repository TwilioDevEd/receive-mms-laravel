<a href="https://www.twilio.com">
  <img src="https://static0.twilio.com/marketing/bundles/marketing/img/logos/wordmark-red.svg" alt="Twilio" width="250" />
</a>

# Recieve SMS and MMS Messages. Powered by Twilio - PHP/Laravel

[![Build
Status](https://travis-ci.org/TwilioDevEd/receive-mms-laravel.svg?branch=master)](https://travis-ci.org/TwilioDevEd/receive-mms-laravel)

#TODO: update this link
Use Twilio to receive SMS and MMS messages. For a step-by-step tutorial see [twilio docs](https://www.twilio.com/docs/tutorials/walkthrough/appointment-reminders/node/express).

## Local development

First you need to install [PHP](http://php.net/) and [Composer](https://getcomposer.org/).

To run the app locally:

1. Clone this repository and `cd` into it

   ```bash
   git clone git@github.com:TwilioDevEd/receive-mms-laravel.git && \

   cd receive-mms-laravel
   ```

1. Install dependencies

    ```bash
    composer install
    ```

1. Copy the sample configuration file and edit it to match your configuration

   ```bash
   $ cp .env-example .env
   ```
   You can find your `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN` in your
   [Twilio Account Settings](https://www.twilio.com/console).
   You will also need a `TWILIO_NUMBER`, which you may find [here](https://www.twilio.com/console/phone-numbers/incoming).

   Run `source .env` to export the environment variables

1. Create database file and run migrations:
    ```bash
    touch ./database/database.sqlite && \
    php artisan migrate --force
    ```

1. Run the application

    ```bash
    php artisan serve --port 8000
    ```
1. Run ngrok:

    ```
    ngrok http 8000
    ```
  Be sure to copy the ngrok http url and associate it with your Twilio Phone Number
  in your [twilio console](twilio.com/console.). The incoming SMS webhook url for
  your number should be as follows:
  `https://<given-ngrok-domain>/api/incoming`

1. Check it out at [http://localhost:8000](http://localhost:8000)

That's it

## Run the tests

You can run the tests locally by typing

```bash
phpunit
```

## With Docker

1. Copy the sample configuration file and edit it to match your configuration

   ```bash
   $ cp .env-example .env
   ```
   You can find your `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN` in your
   [Twilio Account Settings](https://www.twilio.com/console).
   You will also need a `TWILIO_NUMBER`, which you may find [here](https://www.twilio.com/console/phone-numbers/incoming).


   ```
   docker run --env-file .env -it twiliodeved/receive-mms-laravel
   ```

   You will see the ngrok status console with your given url. Copy this url and set it as
   your incoming SMS webhook in your [twilio console](twilio.com/console.). The
   incoming SMS webhook url for your number should be as follows:
  `https://<given-ngrok-domain>/api/incoming`

  Finally, open the ngrok link in your browser. (`https://<given-ngrok-domain>`)

  Local development:

  ```
  docker run --env-file .env -it -v $PWD:/home/dev/src twiliodeved/receive-mms-laravel
  ```

## Meta

* No warranty expressed or implied. Software is as is. Diggity.
* [MIT License](http://www.opensource.org/licenses/mit-license.html)
* Lovingly crafted by Twilio Developer Education.
