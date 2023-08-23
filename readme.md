# Test siotest

## Requirements:
- **Linux**
- **docker-ce** (Docker version 24.0.5, build ced0996)
- **docker-compose** (docker-compose version 1.29.2, build unknown)

---

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
# Install PHPUnit
docker exec -it sio_php bash -c 'vendor/bin/simple-phpunit install'
```

```bash
# Create test environment
docker exec -it sio_php bash -c 'composer run-script create-test-environment'
```

```bash
# Run tests
docker exec -it sio_php bash -c 'vendor/bin/simple-phpunit'
```

```bash
# Drop test environment
docker exec -it sio_php bash -c 'composer run-script drop-test-environment'
```

## Other:

```bash
# Drop database
docker exec -it sio_php bash -c 'symfony console doctrine:database:drop --force'
```

```bash
# Get version PHPUnit
docker exec -it sio_php bash -c 'vendor/bin/simple-phpunit --version'
```

---

## Package issues

> Cannot use [**DAMADoctrineTestBundle**](https://github.com/dmaicher/doctrine-test-bundle),
according to [documentation](https://symfony.com/doc/current/testing.html#configuring-a-database-for-tests).
[Issue](https://github.com/dmaicher/doctrine-test-bundle/issues/188)
is expected [to be resolved](https://github.com/dmaicher/doctrine-test-bundle/issues/188#issuecomment-1636986170)
