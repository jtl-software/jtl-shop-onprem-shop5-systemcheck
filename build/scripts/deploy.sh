#!/usr/bin/env bash

PROJECT_NAME=$1;
REF=$2;
CONVERT_REF="${REF//[\/\.]/-}";
FILENAME="${PROJECT_NAME}-${CONVERT_REF}.zip";
ARCHIVE_PATH="${3}/${FILENAME}";
ZIP_PATH=$4;

echo "";
echo "Create zip of build '${REF}'...";

zip -r -q ${ARCHIVE_PATH} ${ZIP_PATH} -x \*.git* \*.idea* \*docs/* \*patch-dir-* \*tests/* \*build/* \*node_modules/* \*.asset_cs \*.php_cs \*.travis.yml \*phpunit.xml;
echo "  ${FILENAME}";
echo "";