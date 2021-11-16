## Kurulum Notları

Eğer memory error verirse

php -d memory_limit=-1 /usr/local/bin/composer require PAKET ADRESİ

veya

php -d memory_limit=-1 /usr/local/bin/composer require tymon/jwt-auth --ignore-platform-reqs


yeni kurulumda .env dosyasını update etmeyi unutma.
