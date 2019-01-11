#! /bin/bash

set -u
set -e

if [ -z ${1} ]; then
	echo "Release version (arg1) not set !"
	exit 1;
fi

SRC_DIR=`dirname $0`"/.."
RELEASE_VERSION=${1}
echo "Release version set to ${RELEASE_VERSION}"

sed -ri 's/(.*)<version>(.+)<\/version>/\1<version>'${RELEASE_VERSION}'<\/version>/g' ${SRC_DIR}/appinfo/info.xml
git commit -am "Release "${RELEASE_VERSION}
git tag ${RELEASE_VERSION}
git push
git push --tags
# Wait a second for Github to ingest our data
sleep 1
cd /tmp
rm -Rf weather-packaging && mkdir weather-packaging && cd weather-packaging

# Download the git file from github
wget https://github.com/nextcloud/weather/archive/${RELEASE_VERSION}.tar.gz
tar xzf ${RELEASE_VERSION}.tar.gz
mv weather-${RELEASE_VERSION} weather

# Drop unneeded files
rm -Rf \
	weather/js/devel \
	weather/gulpfile.js \
	weather/package.json \
	weather/.ci \
	weather/.tx \
	weather/doc

tar cfz weather-${RELEASE_VERSION}.tar.gz weather
echo "Release version "${RELEASE_VERSION}" is now ready."
