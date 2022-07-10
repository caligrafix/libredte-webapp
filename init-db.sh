#! /bin/sh

# This is the script that run as the init container's command
# Loads initial data into the database

# Set the same defaults as in core-dist.php
: "${PGUSER:=libredte}"
: "${PGDATABASE:=libredte}"

export PGUSER
export PGDATABASE

# Check if database is already initialized
# dte_get_detalle is one of the last thing to be created, so if it exists
# it means the database initialization is complete
echo "select pg_get_functiondef('dte_get_detalle(text)'::regprocedure);" | psql -v ON_ERROR_STOP=1 >/dev/null
RESULT=$?
if [ $RESULT -eq 0 ]; then
    echo "Database already initialized"
    exit 0
fi

psql libredte < /usr/share/sowerphp/extensions/sowerphp/app/Module/Sistema/Module/Usuarios/Model/Sql/PostgreSQL/usuarios.sql
psql libredte < ./website/Module/Sistema/Module/General/Model/Sql/PostgreSQL/actividad_economica.sql
psql libredte < ./website/Module/Sistema/Module/General/Model/Sql/PostgreSQL/banco.sql
psql libredte < /usr/share/sowerphp/extensions/sowerphp/app/Module/Sistema/Module/General/Module/DivisionGeopolitica/Model/Sql/PostgreSQL/division_geopolitica.sql
psql libredte < /usr/share/sowerphp/extensions/sowerphp/app/Module/Sistema/Module/General/Model/Sql/moneda.sql
psql libredte < ./website/Module/Dte/Model/Sql/PostgreSQL.sql

psql libredte < ./website/Module/Dte/Model/Sql/dte_plus.sql
