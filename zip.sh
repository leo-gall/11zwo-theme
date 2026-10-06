#!/bin/sh
# Theme-Zip zum Hochladen bauen: kompiliert Tailwind, PHP, JavaScript und CSS
# und legt wp-content/themes/11zwo.wp-JJJJ-MM-TT-HHMM.zip an (ältere Zips weg).
set -e
cd "$(dirname "$0")"
[ -d node_modules ] || npm install --silent
npm run --silent release
