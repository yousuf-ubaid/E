#!/bin/bash
set -e

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

#env
sed -i "s#value_email_smtp_host#${SMTP_HOST}#g" application/config/env.php
sed -i "s#value_email_smtp_username#${SMTP_USERNAME}#g" application/config/env.php
sed -i "s#value_email_smtp_password#${SMTP_PASSWORD}#g" application/config/env.php
sed -i "s#value_email_smtp_port#${SMTP_PORT}#g" application/config/env.php
sed -i "s#value_email_smtp_from#${SMTP_FROM_ADDRESS}#g" application/config/env.php

sed -i "s#value_vendor_portal_api_base_url#${VENDOR_PORTAL_API_BASE_URL}#g" application/config/env.php
sed -i "s#value_vendor_portal_api_username#${VENDOR_PORTAL_API_USERNAME}#g" application/config/env.php
sed -i "s#value_vendor_portal_api_password#${VENDOR_PORTAL_API_PASSWORD}#g" application/config/env.php

sed -i "s#value_db_host#${DB_HOST}#g" application/config/env.php
sed -i "s#value_db_username#${DB_USERNAME}#g" application/config/env.php
sed -i "s#value_db_password#${DB_PASSWORD}#g" application/config/env.php
sed -i "s#value_db_database#${DB_NAME}#g" application/config/env.php
sed -i "s#value_pay_pal_client_id#${PAY_PAL_CLIENT_ID}#g" application/config/env.php
sed -i "s#value_pay_pal_secret_key#${PAY_PAL_SECRET_KEY}#g" application/config/env.php
sed -i "s#value_ftp_host_name#${FTP_HOST_NAME}#g" application/config/env.php
sed -i "s#value_ftp_username#${FTP_USERNAME}#g" application/config/env.php
sed -i "s#value_ftp_password#${FTP_PASSWORD}#g" application/config/env.php
sed -i "s#value_ftp_host#${FTP_HOST}#g" application/config/env.php
sed -i "s#value_QHSE_login_url#${QHSE_LOGIN_URL}#g" application/config/env.php
sed -i "s#value_qhse_authorization#${QHSE_AUTHORIZATION}#g" application/config/env.php
sed -i "s#value_vendor_portal_url#${VENDOR_PORTAL_URL}#g" application/config/env.php
sed -i "s#value_google_map_key#${GOOGLE_MAP_KEY}#g" application/config/env.php

sed -i "s#value_spur_go_DB_HOST#${SPUR_GO_DB_HOST}#g" application/config/env.php
sed -i "s#value_spur_go_DB_USER#${SPUR_GO_DB_USER}#g" application/config/env.php
sed -i "s#value_spur_go_DB_PASSWORD#${SPUR_GO_DB_PASSWORD}#g" application/config/env.php
sed -i "s#value_spur_go_DB_NAME#${SPUR_GO_DB_NAME}#g" application/config/env.php
sed -i "s#value_host_url#${HOST_URL}#g" application/config/env.php
sed -i "s#value_email_token#${EMAIL_TOKEN}#g" application/config/env.php
sed -i "s#value_from_email_url#${FROM_EMAIL_URL}#g" application/config/env.php

#s3
sed -i "s#value_bucket_name#${BUCKET_NAME}#g" application/config/s3.php
sed -i "s#value_bucket_region#${BUCKET_REGION}#g" application/config/s3.php
sed -i "s#value_bucket_access_key#${BUCKET_ACCESS_KEY}#g" application/config/s3.php
sed -i "s#value_bucket_secret_key#${BUCKET_SECRET_KEY}#g" application/config/s3.php

echo "configurations completed"
