#!/bin/sh
# RIQX: regenerate the right-to-left copies of the console stylesheets.
# Run after merging upstream CortenDesk (CI fails if the copies are stale).
set -e
cd "$(dirname "$0")/../public/assets/css"
for f in spacing shell cortendesk; do
    npx --yes rtlcss@4.3.0 --silent --config ../../../riqx/rtlcss.json "$f.css" "$f.rtl.css"
done
