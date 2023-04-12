#!/usr/bin/env bash

function cleanup()
{
  TOKILL="$(jobs -p)"
	echo "[Master] Kill processes $TOKILL"
	kill $TOKILL
}


function printUsage()
{
    echo "Usage: master_update.sh slaves timeout php"
}


trap cleanup SIGINT SIGTERM

#WORKDIR="/mnt/hgfs/vm_shared/smm-api"
PIDS=""
SLAVES=$1
TIMEOUT=$2
PHP=$3

if [ "$SLAVES" == "0" ]; then
    exit 0;
fi

if [ "$SLAVES" == "" ]; then
    printUsage; exit 0;
fi

if [ "$TIMEOUT" == "" ]; then
    printUsage; exit 0;
fi

if [ "$PHP" == "" ]; then
    printUsage; exit 0;
fi

#cd $WORKDIR || { echo "[Master] Could not cd to WORKDIR"; exit 1; }

for (( i = 0; i < $SLAVES; i++))
do
	$PHP artisan ss:slave $i &
	PIDS="$PIDS $!"
	echo "[Master] Started slave id $i pid $!"
done

echo "[Master] waiting 1 second."
sleep 1

echo "[Master] starting update..."
$PHP artisan ss:update $SLAVES $TIMEOUT

echo "[Master] is waiting for slaves to finish."
wait $PIDS
echo "[Master] Exit."
