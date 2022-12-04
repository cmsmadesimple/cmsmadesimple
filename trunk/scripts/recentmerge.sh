#!/bin/bash

git log --after="2017-12-31" --pretty=format:"%h :  %s" origin/master


git fetch
git checkout $FROMBRANCH
git pull
git checkout $TOBRANCH
git pull
git checkout $FROMBRANCH
if [[ 'init' = $INIT ]]; then
  CHERRIES=$(git log --format="%H" --reverse --cherry-pick --no-merges $TOBRANCH..$FROMBRANCH $REPOPATH)
else
  CHERRIES=$(git log --after=`git log $TOBRANCH -n 1 --format="%aI"` --format="%H" --reverse --cherry-pick --no-merges $TOBRANCH..$FROMBRANCH $REPOPATH)
fi
git checkout $TOBRANCH
for CHERRY in $CHERRIES
do
  git cherry-pick --ff $CHERRY
done


git log --after="2017-12-31" --format="%H" --reverse --cherry-pick --no-merges local_stager..origin/master phar_installer
git checkout local_stager


git cherry-pick --ff bf71786a1fbcb8a6346dac012c04c1fd74e1c1e3

git cherry-pick --ff c70100197e55a52cf7b9f08a2a82c691808b036b

git cherry-pick --ff 2342fdc0f5e9256f5cf8482ca1a72f3857a9a84c

