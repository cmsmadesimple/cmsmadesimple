#!/bin/sh

version=$1

if [ "$version" = "" ]; then
	echo "No version given"
	exit 1
fi

svn export http://svn.cmsmadesimple.org/svn/cmsmadesimple/trunk cmsmadesimple-${version}
cd cmsmadesimple-${version}
sh autogen.sh
sh release-cleanup.sh
touch tmp/cache/SITEDOWN
svn import http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${version} -m "-- Release ${version} --" --no-auto-props
cd ..

#svn export http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${version} cmsmadesimple-${version}
tar zcf cmsmadesimple-${version}.tar.gz cmsmadesimple-${version}
mv cmsmadesimple-${version} cmsmadesimple
zip -r cmsmadesimple-${version}.zip cmsmadesimple
md5sum cmsmadesimple-${version}.*
rm -fr cmsmadesimple
