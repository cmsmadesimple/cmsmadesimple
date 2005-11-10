#!/bin/sh

oldversion=$1
newversion=$2

if [ "$oldversion" = "" ]; then
	echo "No old version given"
	exit 1
fi

if [ "$newversion" = "" ]; then
	echo "No new version given"
	exit 1
fi

mkdir cmsmadesimple-diff-${oldversion}-${newversion}
svn co http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${newversion} cmsmadesimple-${newversion}
cd cmsmadesimple-${newversion}
svn diff http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${oldversion} http://svn.cmsmadesimple.org/svn/cmsmadesimple/tags/version-${newversion} | grep "Index: " | cut -d " " -f 2 | xargs -i cp --parents {} ../cmsmadesimple-diff-${oldversion}-${newversion}
cd ..
cd cmsmadesimple-diff-${oldversion}-${newversion}
sh ../cmsmadesimple-${newversion}/release-cleanup.sh
tar zcf cmsmadesimple-diff-${oldversion}-${newversion}.tar.gz *
mv cmsmadesimple-diff-${oldversion}-${newversion}.tar.gz ..
zip -r cmsmadesimple-diff-${oldversion}-${newversion}.zip *
mv cmsmadesimple-diff-${oldversion}-${newversion}.zip ..
cd ..
rm -fr cmsmadesimple-${newversion}
rm -fr cmsmadesimple-diff-${oldversion}-${newversion}
