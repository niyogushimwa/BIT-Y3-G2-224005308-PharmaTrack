# PharmaTrack — 2:50 demo video + voiceover script

**Recorded video:** `demo/PharmaTrack_Demo.mp4` (2 minutes 50 seconds, 1280×720, silent)
**Recording log:** `demo/demo_recording_log.json` (every action with its timestamp)

The video is a real screen recording of the site, driven through the full workflow. It has **no audio track** — read the voiceover below while it plays, or paste the text into a free editor (Clipchamp, CapCut, iMovie) as subtitles/voiceover.

Site URL: `http://localhost/Pharmacy-Stock-Alert-System2/pharmacy-stock-alert/public/`

---

## Timed script — what is on screen and what to say

| Time | On screen | Say this |
|---|---|---|
| **0:00–0:08** | Sign-in page with hero panel. `admin` / `admin123` entered, then Sign in. | "Hi, this is PharmaTrack — a pharmacy stock alert system built with PHP, MySQL and plain JavaScript. I'm signing in as the administrator so we can see the whole workspace." |
| **0:08–0:25** | Dashboard: 14 medicines, 5 low stock, 4 expired, 9 active alerts, 883 units; low-stock list, expiry watch, recent alerts, category bars. | "This is the overview. Fourteen medicines in the catalog, five at or below their reorder level, four already expired, and nine active alerts across 883 units on hand. The low-stock list, expiry watch, category breakdown and latest activity are all on one screen." |
| **0:25–0:35** | Medicines catalog; status filter changed to "Low stock" and Filter clicked. | "The medicines page is the main inventory catalog. Each row shows stock, the reorder threshold, expiry date, supplier and a status badge. I can filter by status — here are only the low-stock products." |
| **0:35–0:47** | "Live medicine search" panel; typing `para` produces 2 JSON-backed results. | "There's also a live lookup. As I type 'para', results appear instantly from a small JSON API, without reloading the page." |
| **0:47–0:58** | Add medicine form filled: Amoxicillin 250mg, 5 units, reorder 20, expiry 2027-12-31, supplier 1. Saved. | "Adding stock is a simple validated form. I'll add Amoxicillin 250 milligrams, five units in stock, reorder at twenty, expiring next year, linked to a supplier." |
| **0:58–1:12** | Back on dashboard: totals now 15 medicines and a new low-stock alert. | "Back on the dashboard, the totals update automatically. Fifteen medicines now, and a new low-stock alert — because five units is below the reorder level of twenty." |
| **1:12–1:35** | Alerts page; alert history scrolled; an alert is resolved; filter switched to "Resolved alerts". | "The alerts page is where the team takes action. Low-stock and expired alerts are generated automatically. I can resolve an alert to record the follow-up, and the status filters let me review active, resolved, or the full alert history." |
| **1:35–1:52** | Suppliers page, then the add-supplier form, then back to the list of four suppliers. | "Suppliers are managed separately, and medicines link to them with a foreign key. If a supplier is removed, the medicine stays in the catalog and only the supplier link is cleared." |
| **1:52–2:05** | Users page (admin), then add-user form, then back to the user list. | "User management is administrator-only. Passwords are stored as hashes, and accounts have two roles: administrator and pharmacist." |
| **2:05–2:21** | Sign out; sign in as `remy` / `remy123`; direct visit to the users URL is blocked with a 403 explanation and redirect to dashboard. | "Now I sign out and log in as a pharmacist. Notice the Users link is gone. If I try the users page directly, access is blocked and I'm returned to my dashboard with an explanation." |
| **2:21–2:50** | Sign out again; registration page shown; back to the sign-in page. | "Finally, new pharmacists can register themselves, while protected pages and APIs all require a valid session. That's the complete PharmaTrack workflow: track stock, catch expiry and low-stock risks early, and record the follow-up." |

---

## Continuous voiceover text (copy/paste version)

> Hi, this is PharmaTrack — a pharmacy stock alert system built with PHP, MySQL and plain JavaScript. I'm signing in as the administrator so we can see the whole workspace.
>
> This is the overview. Fourteen medicines in the catalog, five at or below their reorder level, four already expired, and nine active alerts across 883 units on hand. The low-stock list, expiry watch, category breakdown and latest activity are all on one screen.
>
> The medicines page is the main inventory catalog. Each row shows stock, the reorder threshold, expiry date, supplier and a status badge. I can filter by status — here are only the low-stock products.
>
> There's also a live lookup. As I type "para", results appear instantly from a small JSON API, without reloading the page.
>
> Adding stock is a simple validated form. I'll add Amoxicillin 250 milligrams, five units in stock, reorder at twenty, expiring next year, linked to a supplier.
>
> Back on the dashboard, the totals update automatically. Fifteen medicines now, and a new low-stock alert — because five units is below the reorder level of twenty.
>
> The alerts page is where the team takes action. Low-stock and expired alerts are generated automatically. I can resolve an alert to record the follow-up, and the status filters let me review active, resolved, or the full alert history.
>
> Suppliers are managed separately, and medicines link to them with a foreign key. If a supplier is removed, the medicine stays in the catalog and only the supplier link is cleared.
>
> User management is administrator-only. Passwords are stored as hashes, and accounts have two roles: administrator and pharmacist.
>
> Now I sign out and log in as a pharmacist. Notice the Users link is gone. If I try the users page directly, access is blocked and I'm returned to my dashboard with an explanation.
>
> Finally, new pharmacists can register themselves, while protected pages and APIs all require a valid session. That's the complete PharmaTrack workflow: track stock, catch expiry and low-stock risks early, and record the follow-up.

---

## If you are asked to do a LIVE demo instead

1. Keep XAMPP Apache + MySQL running.
2. Open the site URL above and sign in with `admin` / `admin123`.
3. Follow the table in order; each step takes roughly 10–20 seconds.
4. Demo accounts: `admin/admin123`, `remy/remy123`, `claire/claire123`.
5. The database has been reset to the original seed data (14 medicines, 4 suppliers, 9 alerts), so the live numbers match the video.
6. If the data ever goes missing, re-import `database/schema.sql` and `database/seed.sql` in phpMyAdmin. MySQL may report "Duplicate entry 'admin'" on re-import — that is harmless; the other seed rows still load.
