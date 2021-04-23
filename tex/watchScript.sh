#!/bin/bash

while true; do
    clear
    date
    ./aplicar.py cypxt_online_call.csv
    inotifywait -e modify *.csv *.tex *.py
done
