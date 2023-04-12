up:
	docker-compose up -d

build:
	docker-compose up --build -d

down:
	docker-compose down

test:
	docker-compose exec api_application vendor/bin/phpunit

perm:
	sudo chown ${USER}:${USER} bootstrap/cache -R
	sudo chown ${USER}:${USER} storage -R

migrate:
	docker-compose exec api_application php artisan migrate:fresh --seed

tinker:
	docker-compose exec api_application php artisan tinker
