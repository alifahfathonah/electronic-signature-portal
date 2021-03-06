# Electronic signature portal
Self-hosted electronic signature portal:
- Specify admins,
- Restrict access the portal,
- Upload documents for signing,
- Specify signees, share document link,
- Track document signatures.

This project uses the eID Easy API to simplify the generation of Qualified Electronic Signatures - the highest grade of electronic signatures.

## This is initial preview
This project was developed in Garage48 and as of this moment is not fully tested and not fully complete yet. If you are interested in getting notified when stable version is ready then write to info@eideasy.com.

## Developer workflow
Wish to make changes to the codebase? There are several ways to set this project up on your dev environment. Here's the easiest one.
Since this app uses browscap to detect browser and OS then you need to install it also if missing

To set up the back-end:
```
0.0.0.0         signing-portal.dev
0.0.0.0         front.signing-portal.dev

cd back-end
composer install
vendor/bin/browscap-php browscap:update
php artisan serve
```

To set up the front-end
```
cd front-end
npm i
npm run dev
```

If you wish the back-end to be run on a different domain, add a .env file to `/front-end`:
```
BASE_URL=http://your-backend.dev/
```

Make sure to re-run `npm run dev` after updating the .env file.

When using `npm run dev`, you must set `BYPASS_CSRF=true` in `back-end/.env`.

To build the front-end for production
```
npm run generate
```
This generates the front-end files and places them in `/back-end/public`. Then, it copies files from `/back-end/public-base/` to `/back-end/public/`.
