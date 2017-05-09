#!/bin/sh
HOST='123.201.110.194'
USER='hd'
PASSWD='9DrICc179Tc1apg'
FILE='C:\Users\pankita\Documents\test_image_facetag'
REMOTEPATH='/facetag/uploads/automatic_upload/'business_1/icp_1
cd $FILE
ftp -n $HOST <<END_SCRIPT
quote USER $USER
quote PASS $PASSWD
cd $REMOTEPATH
prompt off
mput *.*
quit
END_SCRIPT
exit 0