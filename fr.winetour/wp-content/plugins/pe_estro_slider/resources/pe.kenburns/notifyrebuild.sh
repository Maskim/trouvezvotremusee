#! /bin/sh

inotifywait -m -r -e close_write --format="%w%f" -r src/ 2>/dev/null | while read i; do
	./compress.sh
done
