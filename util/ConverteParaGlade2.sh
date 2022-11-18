../util/tiracentos.sh $1.glade > gladetmp
libglade-convert gladetmp > $1.glade2
rm gladetmp
