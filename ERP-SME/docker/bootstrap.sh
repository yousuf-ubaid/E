#!/bin/bash
set -e
#copying
echo "copying files"
cp application/config/config.example.php application/config/config.php
cp application/config/database.example.php application/config/database.php
cp application/config/env.example.php application/config/env.php
cp application/config/s3.example.php application/config/s3.php

echo "updating configurations"

#config
sed -i "s#value_base_url#${BASE_URL}#g" application/config/config.php

#db
sed -i "s#value_db_hostname#${DB_HOST}#g" application/config/database.php
sed -i "s#value_db_username#${DB_USERNAME}#g" application/config/database.php
sed -i "s#value_db_password#${DB_PASSWORD}#g" application/config/database.php
sed -i "s#value_db_database#${DB_NAME}#g" application/config/database.php
sed -i "s#value_empty_db_database#${EMPTY_DB_NAME}#g" application/config/database.php

#env
sed -i "s#value_db_hostname#${DB_HOST}#g" application/config/env.php
sed -i "s#value_db_username#${DB_USERNAME}#g" application/config/env.php
sed -i "s#value_db_password#${DB_PASSWORD}#g" application/config/env.php
sed -i "s#value_db_database#${DB_NAME}#g" application/config/env.php

#s3
sed -i "s#value_bucket_name#${BUCKET_NAME}#g" application/config/s3.php
sed -i "s#value_bucket_region#${BUCKET_REGION}#g" application/config/s3.php
sed -i "s#value_bucket_access_key#${BUCKET_ACCESS_KEY}#g" application/config/s3.php
sed -i "s#value_bucket_secret_key#${BUCKET_SECRET_KEY}#g" application/config/s3.php

echo "configurations completed"
