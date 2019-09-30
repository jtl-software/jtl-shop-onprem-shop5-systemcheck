#!/usr/bin/env bash

build_create()
{
    # $1 repository dir
    export REPOSITORY_DIR=$1;

    echo "Executing composer";
    composer install --no-dev -q -d ${REPOSITORY_DIR};
}


(build_create $*)