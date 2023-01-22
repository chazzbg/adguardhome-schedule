#!/usr/bin/ash
SECRET_FILE=/data/.secret

if [ ! -w "$(dirname "$DATABASE_FILE")" ]; then
  echo "Insufficient permissions for writing in $(dirname "$DATABASE_FILE")"
  exit 1
fi

if [ -z "${APP_SECRET}" ]; then
  if [ ! -f  ${SECRET_FILE} ]; then
    ( < /dev/urandom tr -dc '[:alpha:]' | fold -w 32 | head -n 1 ) > ${SECRET_FILE}
  fi
  APP_SECRET=$( cat "$SECRET_FILE")
  export APP_SECRET
fi

if [ -z "${DATABASE_FILE}" ]; then
  DATABASE_FILE="$PWD/var/data.db"
fi

export DATABASE_URL="sqlite:///$DATABASE_FILE"

php bin/console cache:clear

if [ ! -f "$DATABASE_FILE" ]; then
  php bin/console doctrine:database:create
  php bin/console doctrine:schema:create
fi

if ! php bin/console doctrine:schema:validate --quiet; then
  php bin/console doctrine:schema:update --force --complete
fi

exec "$@"
