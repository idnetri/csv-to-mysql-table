# csv-to-mysql-table
Export csv files into MySQL table using PHP auto create table columns in varchar type

1. Copy config-sample.php to config.php, and edit according to db config
2. Create db csvtotable, then import csvtotable.sql in db directory
3. Create /uploads folder (if using mac, don't forget to change permission to read and write)
4. Check these configs on php.ini
    mysqli.allow_local_infile = On
    ini_set('post_max_size','8000M');
    ini_set('upload_max_filesize','8000M');
