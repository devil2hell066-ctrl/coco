# COCO – PHP + MySQL Version

This is your original static site converted to run on **PHP with a MySQL database**.
All CSS (`style.css`, `add-bag.css`) is untouched — same look, same design.

## What changed
| Before | Now |
|---|---|
| `index.html`, `login.html`, `collection.html`, `add-bag.html` | `index.php`, `login.php`, `collection.php`, `add-bag.php` |
| Products hardcoded in HTML/JS | Stored in a `products` MySQL table, rendered with PHP |
| Login was fake (just a JS timeout) | Real login against a `users` table (hashed passwords) |
| No signup page | Added `register.php` |
| Cart stored in `localStorage` | Cart stored in a `cart` table, tied to your PHP session |
| No real checkout | `checkout.php` saves the order into `orders` / `order_items` tables |

New files: `config.php` (DB connection), `database.sql` (schema + seed data),
`includes/functions.php` (helpers), `register.php`, `logout.php`,
`cart_add.php`, `cart_update.php`, `cart_remove.php`, `cart_coupon.php`, `checkout.php`.

## Setup (XAMPP / WAMP / LAMP)

1. **Copy this whole folder** into your server's web root, e.g. `htdocs/coco`.

2. **Create the database.** Open phpMyAdmin (or the MySQL CLI) and import `database.sql`.
   This creates the `coco_db` database with `users`, `products`, `cart`, `orders`,
   and `order_items` tables, and seeds 12 sample products.

   ```
   mysql -u root -p < database.sql
   ```

3. **Check `config.php`** — the defaults (`localhost`, `coco_db`, user `root`, empty password)
   match a typical XAMPP install. Change `DB_USER` / `DB_PASS` if your MySQL differs.

4. **Visit the site**, e.g. `http://localhost/coco/index.php`.

5. **Create an account** at `register.php`, then sign in at `login.php`.
   You can also add products, browse the collection, add items to your bag,
   and place a real order — everything now round-trips through MySQL.

## Notes
- Passwords are stored using PHP's `password_hash()` (bcrypt) — never plain text.
- The cart is tied to the PHP session, so it works for guests too and survives
  a login (same browser session).
- Coupon codes: `WELCOME10`, `LUXURY20`, `COCO50` — validated server-side in
  `cart_coupon.php`, so the discount can't be tampered with from the browser.
- `checkout.php` recalculates totals from the database (never trusts the
  client), inserts the order, then empties the cart.
