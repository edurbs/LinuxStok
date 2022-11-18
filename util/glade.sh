#!/bin/sh
# utiliza o iconv para modificar o encoding glade para aparecer os acentos
# este e usado ANTES de abrir o glade
ICONV=/usr/bin/iconv
mkdir tmp
$ICONV -f iso8859-1 -t utf8 "$1" -o tmp/"$1"
if [ $? != 0 ] ; then
    echo "Couldn't ping localhost, weird"
    rm tmp -r -f
    exit 0
fi
cp tmp/"$1" "$1" -f
rm tmp -r -f

glade $1

# utiliza o iconv para modificar o encoding glade para aparecer os acentos
# use DEPOIS de abrir o glade
ICONV=/usr/bin/iconv
mkdir tmp
$ICONV -f utf8 -t iso8859-1 "$1" -o tmp/"$1"
if [ $? != 0 ] ; then
    echo "Couldn't ping localhost, weird"
    rm tmp -r -f
    exit 0
fi
cp tmp/"$1" "$1" -f
rm tmp -r -f
exit 0
