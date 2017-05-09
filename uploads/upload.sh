#!/bin/sh
HOST='192.168.1.202'
USER='hd'
PASSWD='9DrICc179Tc1apg'
FILE='C:\wamp\www\test\123.txt'

ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASSWD
put $FILE
quit
END_SCRIPT
exit 0
