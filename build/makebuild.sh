#!/bin/sh

version=$1

if [ "$version" = "" ]; then
	echo "No version given"
	exit 1
fi

svn co http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${version} cmsmadesimple-${version}
cd cmsmadesimple-${version}
sh autogen.sh
sh release-cleanup.sh
touch tmp/cache/SITEDOWN
cd ..
tar zcf cmsmadesimple-${version}.tar.gz cmsmadesimple-${version}
mv cmsmadesimple-${version} cmsmadesimple
zip -r cmsmadesimple-${version}.zip cmsmadesimple
md5sum cmsmadesimple-${version}.*
rm -fr cmsmadesimple
