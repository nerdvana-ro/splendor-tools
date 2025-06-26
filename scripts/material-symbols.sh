#!/usr/bin/bash

# Creează un subset al fontului Material Symbols, doar cu simbolurile dorite.
SRC=~/Desktop/MaterialSymbolsRounded_Filled-Regular.ttf

# Folosite:
# play_arrow
# replay
# skip_next
# star
# upload

fonttools subset $SRC \
    --unicodes=5f-7a,30-39,e037,e042,e044,e838,f09b \
    --no-layout-closure \
    --output-file=viewer/font/material-symbols.woff2 \
    --flavor=woff2
