#!/bin/bash

CONFIG_SUFFIX_BAK=.bak

cd $(dirname $0)/../
sed -i$CONFIG_SUFFIX_BAK -e "s|APP_VERSION=.*|APP_VERSION=${VERSION}|g" .env
rm -f ./.env.bak
