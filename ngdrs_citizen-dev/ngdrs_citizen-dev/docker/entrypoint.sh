#!/bin/sh
set -eu

IMAGE_BUILD_DIR=/opt/ngdrs/public-build
MOUNTED_BUILD_DIR=/var/www/public/build
READY_MARKER=/tmp/ngdrs-public-assets-ready

rm -f "$READY_MARKER"

if [ ! -f "$IMAGE_BUILD_DIR/manifest.json" ]; then
    echo "Vite build manifest is missing from the image: $IMAGE_BUILD_DIR/manifest.json" >&2
    exit 1
fi

mkdir -p "$MOUNTED_BUILD_DIR/assets"
cp -a "$IMAGE_BUILD_DIR/." "$MOUNTED_BUILD_DIR/"

php artisan optimize:clear

touch "$READY_MARKER"
exec "$@"