FILE=detoxify.zip
if test -f "$FILE"; then
    rm $FILE
fi
zip -r $FILE . -x "*.sh" -x ".env" -x "vendor*" -x "node_modules*"