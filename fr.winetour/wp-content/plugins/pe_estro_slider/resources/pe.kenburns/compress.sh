#! /bin/sh

sources=*.html;

packs="packs"
packname="pack"
minified="minified"
compressed="compressed"
tmp="/dev/shm/skin.css"
#compressor="YUICompressor"
compressor="/home/makoomba/scripts/cachedYUI.sh"
#compressor="/bin/cat"

IFN="
"

cd themes

dest="allskins.min.css"
$compressor common.css > $dest
		
for t in *; do 
	[ -d "$t" ] || continue
	echo "skin $t"
	sed "s|\.peKenBurns |.peKenBurns.$t |g;s|img/|$t/img/|" "$t/style.css"   > $tmp
	$compressor $tmp >> $dest
	rm -f $tmp
done


for t in *; do 
	[ -d "$t" ] || continue
	dest="$t/skin.min.css"
	echo "skin $t"
	$compressor common.css > $dest
	$compressor "$t/style.css" >> $dest
done

cd ..


dest="jquery.pixelentity.kenburnsSlider.min.js"

rm $dest 2>/dev/null
#for f in src/*.js; do
for f in $(grep -vE "^$|^#" src/build.list); do
	f="src/$f"
	echo "$f"
	$compressor "$f" >> $dest;
	if [ $? -eq 1 ]; then
		echo "ERROR COMPRESSING $f"
		exit 1
	fi
done 
