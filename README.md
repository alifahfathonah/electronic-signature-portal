# Electronic signature portal
Self-hosted electronic signature portal:
- Specify admins,
- Restrict access the portal,
- Upload documents for signing,
- Specify signees, share document link,
- Track document signatures.

This project uses the eID Easy API to simplify the generation of Qualified Electronic Signatures - the highest grade of electronic signatures.

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
NUXT_ENV_BROWSER_BASE_URL=localhost:8000 npm run dev
```
This assumes that `artisan serve` sets your back-end up at `localhost:8000`.

To build the front-end for production
```
npm run generate
```
This generates the front-end files and places them in `/back-end/public`. Then, it copies files from `/back-end/public-base/` to `/back-end/public/`.
