import Database from "better-sqlite3";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const DB_PATH = process.env.DATABASE_PATH ?? path.join(__dirname, "..", "reports.db");

const db = new Database(DB_PATH);

db.exec(`
  CREATE TABLE IF NOT EXISTS damage_reports (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    vehicle_make  TEXT NOT NULL,
    vehicle_model TEXT NOT NULL,
    license_plate TEXT NOT NULL,
    description   TEXT NOT NULL,
    status        TEXT NOT NULL DEFAULT 'draft',
    photos        TEXT NOT NULL DEFAULT '[]',
    created_at    TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now')),
    updated_at    TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now'))
  )
`);

const count = db.prepare("SELECT COUNT(*) as n FROM damage_reports").get().n;
if (count > 0) {
  console.log(`Skipping seed — ${count} reports already exist.`);
  process.exit(0);
}

const insert = db.prepare(`
  INSERT INTO damage_reports (vehicle_make, vehicle_model, license_plate, description, status)
  VALUES (?, ?, ?, ?, ?)
`);

const seed = db.transaction(() => {
  insert.run("Volkswagen", "Golf",     "AB-123-CD", "Front bumper scratched against a concrete pillar in the supermarket parking lot. Paint transfer visible on the left side.", "submitted");
  insert.run("Toyota",    "Yaris",     "GH-456-IJ", "Hailstorm damage across the roof, bonnet, and left rear door. Multiple dents ranging from 1–3 cm in diameter.",           "approved");
  insert.run("BMW",       "3 Series",  "KL-789-MN", "Driver-side mirror assembly knocked off completely while parked in a narrow residential street. Housing cracked.",          "draft");
  insert.run("Renault",   "Clio",      "PQ-321-RS", "Rear-end collision at low speed. Boot lid no longer closes flush; rear bumper cracked on the right corner.",               "submitted");
  insert.run("Ford",      "Focus",     "TU-654-VW", "Keying damage running the full length of the passenger-side doors. Deep scratches through to the primer.",                 "approved");
  insert.run("Peugeot",   "208",       "XY-987-ZA", "Cracked windscreen from a stone chip that spread overnight. Crack originates at the lower-left corner and extends 30 cm.", "draft");
});

seed();
console.log("Seeded 6 damage reports.");
