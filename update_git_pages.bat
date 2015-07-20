@ECHO OFF
echo "Auto update phpdoc in git-hub"
call phpdoc
echo "phpDoc generated"
cd ../utilities-gh-pages
call git add .
call git commit -a -m "update phpDoc"
call git push
echo "phpDoc pushed in git-hub pages branch"
cd ../utilities
