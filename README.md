# HART Muziekschool

Web application for HART Muziekschool in Haarlem.

## Developing

Developing locally requires docker and docker-compose to be installed.

After running `docker-compose up -d --build`, the application is available on http://localhost:8080.

Mailhog is used to capture outgoing emails and is available on http://localhost:8025.

### Accessing the development container
```
docker-compose exec app bash
```

### Composer
For local development, you need to run composer from within the app container to install the dev dependencies.
```composer install```

For copying the vendor directory to the host, run:
```
docker run --rm \
  -v website-hart_vendor_data:/from \
  -v $(pwd)/vendor:/to \
  alpine sh -c "cp -r /from/* /to/"
```

### Run the migrations
To get the database in the correct state, run the migrations.
```./yii migrate```

### Loading fixtures
Loading fixtures seeds the database with some demo data.
```./yii fixture --globalFixtures [] '*'```

### Extracting translations
Extract translations from the source code, then add the required translations to the translation files.
```./yii message/extract messages/extract.php```

TODOS:
- Docent emailen met ongelezen berichten
