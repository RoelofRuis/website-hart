# HART Muziekschool

Web application for HART Muziekschool in Haarlem.

## Ontwikkelen

### Access the development container
```
docker-compose up -d --build

docker-compose exec app bash
```

### Composer
For local development, you need to run composer install in the container to install the dev dependencies.
```composer install```

### Migraties uitvoeren
```./yii migrate```

### Fixtures inladen
```./yii fixture --globalFixtures [] '*'```
