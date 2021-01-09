#!/bin/sh


PS3='Select api version : '
select apiversion in "./src/"*; do
  echo "You selected : $apiversion"
  break
done

PS3='Select Endpoint Base : '
select endpointbase in "$apiversion/Endpoints/"*; do
  echo "You selected : $endpointbase"
  break
done

endpointexactname=${endpointbase##*/}


read -p "Endpoint Name : " endpointname

echo "CREATING ENDPOINT AT $apiversion > $endpointbase > $endpointname";

mkdir $endpointbase/$endpointname
cp -R  $apiversion/Endpoints/backend/boilerplate/ $endpointbase/$endpointname/


echo "Endpoint  Created..."

capitalendpoint="$(tr '[:lower:]' '[:upper:]' <<< ${endpointname:0:1})${endpointname:1}"

cp $endpointbase/$endpointname/Boilerplate.php  "$endpointbase/$endpointname/$capitalendpoint.php"


rm $endpointbase/$endpointname/Boilerplate.php

if [[ "$endpointbase" == *"$endpointexactname"* ]]; then
  if ! grep -q backend/Boilerplate "$endpointbase/$endpointname/$capitalendpoint.php"; then

      sed -i '' 's/backend\\boilerplate/'$endpointexactname'\\boilerplate/g' $endpointbase/$endpointname/$capitalendpoint.php
      sed -i '' 's/backend\\boilerplate/'$endpointexactname'\\boilerplate/g' $endpointbase/$endpointname/delete.php
      sed -i '' 's/backend\\boilerplate/'$endpointexactname'\\boilerplate/g' $endpointbase/$endpointname/get.php
      sed -i '' 's/backend\\boilerplate/'$endpointexactname'\\boilerplate/g' $endpointbase/$endpointname/put.php
      sed -i '' 's/backend\\boilerplate/'$endpointexactname'\\boilerplate/g' $endpointbase/$endpointname/post.php
      echo " Client Endpoint Namespace"
  fi
fi

sed -i '' 's/Boilerplate/'$capitalendpoint'/g' $endpointbase/$endpointname/$capitalendpoint.php

sed -i '' 's/boilerplate/'$endpointname'/g' $endpointbase/$endpointname/$capitalendpoint.php

sed -i '' 's/Boilerplate/'$capitalendpoint'/g' $endpointbase/$endpointname/delete.php

sed -i '' 's/boilerplate/'$endpointname'/g' $endpointbase/$endpointname/delete.php

sed -i '' 's/Boilerplate/'$capitalendpoint'/g' $endpointbase/$endpointname/get.php

sed -i '' 's/boilerplate/'$endpointname'/g' $endpointbase/$endpointname/get.php

sed -i '' 's/Boilerplate/'$capitalendpoint'/g' $endpointbase/$endpointname/post.php

sed -i '' 's/boilerplate/'$endpointname'/g' $endpointbase/$endpointname/post.php

sed -i '' 's/Boilerplate/'$capitalendpoint'/g' $endpointbase/$endpointname/put.php

sed -i '' 's/boilerplate/'$endpointname'/g' $endpointbase/$endpointname/put.php


#PS3='Select api version : '
#options=("Option 12" "Option 2" "Option 3" "Quit")
#select opt in "${options[@]}"; do
#  case $opt in
#  "Option 1")
#    echo "you chose choice 1"
#    ;;
#  "Option 2")
#    echo "you chose choice 2"
#    ;;
#  "Option 3")
#    echo "you chose choice $REPLY which is $opt"
#    ;;
#  "Quit")
#    break
#    ;;
#  *) echo "invalid option $REPLY" ;;
#  esac
#done

