#!/bin/bash
composer install
bin/console cache:clear
symfony server:stop
symfony server:start --no-tls
