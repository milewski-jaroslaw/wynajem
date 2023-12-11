# Wynajem test app job

## Run application

### Start docker:

```shell
docker compose up -d
```

App is on https://localhost

### Login to container:

```shell
docker exec -it wynajem_php bash
```

### Run tests:

```shell
docker exec wynajem_php php artisan test
```

### Stop container:

```shell
docker stop
```

### Stop & remove container:

```shell
docker down
```
