#!/bin/bash
# Esperar o Elasticsearch estar disponível
while ! nc -z seller_tray_elasticsearch 9200; do
  sleep 5
done

echo 'Elasticsearch is ready!'

sleep 5

service cron restart

# Executar o comando Laravel em segundo plano
/usr/local/bin/php /var/www/artisan sales:consume &


# Iniciar o PHP-FPM
exec php-fpm
