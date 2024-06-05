# FileUploadBackend

## Install dependencies

Run `composer install` to install the dependencies

## Update .env

Update DATABASE_URL

## Generate JWT secret key files

Run `php bin/console lexik:jwt:generate-keypair`

## Start server

Run `symfony server:start` for the server to start. Navigate to `http://localhost:8000/`

> Make sure to have the 'admin' user in `user` database table
