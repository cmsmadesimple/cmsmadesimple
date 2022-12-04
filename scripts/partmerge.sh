#!/bin/bash
#see https://blog.ftwr.co.uk/archives/2017/11/15/easy-partial-branch-merging-in-git
#args - from-branch to-branch path init
FROMBRANCH=$1
TOBRANCH=$2
REPOPATH=$3
INIT=$4

if [[ -z "$TOBRANCH" ]]; then
 TOBRANCH='staging'
fi

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
