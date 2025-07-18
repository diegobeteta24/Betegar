# Makefile

.PHONY: dev prod

# -------------------------------------------------------
# Desarrollo: branch develop + instalación + watcher Vite
# -------------------------------------------------------
dev:
	npm ci
	npm run dev -- --host 0.0.0.0

# -------------------------------------------------------
# Producción: branch main + build optimizado + deploy app
# -------------------------------------------------------
prod:
	npm ci --production
	npm run build
	php artisan migrate --force
	php artisan config:cache
	php artisan route:cache
	sudo systemctl reload nginx
