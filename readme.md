# Test siotest

## Install:

```bash
cp docker/.env-dist docker/.env
```

```bash
make build -C docker
make up -C docker
```

```bash
docker exec -it sio_php bash -c 'composer install'
docker exec -it sio_php bash -c 'symfony console siotest:install:app'
```

## Testing:

```bash
# Create test environment
docker exec -it sio_php bash -c 'composer run-script create-test-environment'
```

```bash
# Drop test environment
docker exec -it sio_php bash -c 'composer run-script drop-test-environment'
```

```bash
# Run tests
docker exec -it sio_php bash -c 'php bin/phpunit'
# or
docker exec -it sio_php bash -c 'php vendor/bin/phpunit'
```

## Other:

```bash
# Drop database
docker exec -it sio_php bash -c 'symfony console doctrine:database:drop --force'
```

## Dependencies:
- Linux
- docker-ce
- docker-compose