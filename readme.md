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

## Other:

Drop database
```bash
docker exec -it sio_php bash -c 'symfony console doctrine:database:drop --force'
```

## Dependencies:
- Linux
- docker-ce
- docker-compose