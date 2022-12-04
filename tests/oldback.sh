#!/bin/bash
for file in  'basic_tests.php' 'except_test.php'  'subversion_test.php'  'year_test1.php' 'year_test2.php'; do
 mv -f $file'-old' $file
done
