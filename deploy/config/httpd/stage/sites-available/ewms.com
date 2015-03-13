<VirtualHost *:80>
    ServerAdmin fcasili@stratpoint.com
    ServerName localhost.ssi-store

    DocumentRoot /home/fcasili/Projects/stratpoint_ssiwmsstoreowner/public/
    <Directory /home/fcasili/Projects/stratpoint_ssiwmsstoreowner/public/>
   	 Options Indexes FollowSymLinks MultiViews
   	 AllowOverride All
   	 Order allow,deny
   	 allow from all
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/ewms-error.log

    # Possible values include: debug, info, notice, warn, error, crit,
    # alert, emerg.
    LogLevel warn

    CustomLog ${APACHE_LOG_DIR}/ewms-access.log combined

</VirtualHost>