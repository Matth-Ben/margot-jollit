#!/bin/bash
set -e

CI_ENVIRONMENT_NAME="$1"
PROJECT_ROOT="$2"

cd $PROJECT_ROOT

git pull