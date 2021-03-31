#!/usr/bin/env sh

set -eux

# Upload everything
rsync -rltvzO --delete --exclude-from=.ftpignore --exclude=vendor/ --exclude=node_modules/ ./ "cypxt@cypxt.montandoellocal.com:$WEB_DIR"
