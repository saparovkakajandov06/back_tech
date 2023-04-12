#!/bin/bash

PROJECT_DIR=/var/www/html/smm-api

sudo chown -R www-data.www-data $PROJECT_DIR/storage
sudo chown -R www-data.www-data $PROJECT_DIR/bootstrap/cache
