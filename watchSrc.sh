#!/bin/bash

"$@"
inotifywait -r -m src/ -e modify | while read path action file; do
    "$@"
done