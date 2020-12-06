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

To set up the back-end:
```
cd back-end
composer install
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

This assumes that `artisan serve` sets your back-end up at `localhost:8000`.

To build the front-end for production
```
npm run generate
```
This generates the front-end files and places them in `/back-end/public`. Then, it copies files from `/back-end/public-base/` to `/back-end/public/`.
