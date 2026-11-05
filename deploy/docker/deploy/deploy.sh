#!/usr/bin/env bash
set -e

echo "🚀 Memulai Otomasi Deployment Outgear Lite..."
docker compose exec -T app php artisan down --retry=30
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan optimize
docker compose exec -T app php artisan queue:restart
docker compose exec -T app php artisan up
echo "✅ Deploy selesai!"