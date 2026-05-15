import db from "@/lib/db";

const row = db.prepare("SELECT COUNT(*) as n FROM damage_reports").get() as { n: number };
if (row.n > 0) {
  console.log(`Skipping seed — ${row.n} reports already exist.`);
  process.exit(0);
}

const insert = db.prepare(`
  INSERT INTO damage_reports (vehicle_make, vehicle_model, license_plate, description, status)
  VALUES (?, ?, ?, ?, ?)
`);

db.transaction(() => {
  insert.run("Volkswagen", "Golf",     "AB-123-CD", "Front bumper scratched against a concrete pillar in the supermarket parking lot. Paint transfer visible on the left side.", "submitted");
  insert.run("Toyota",    "Yaris",     "GH-456-IJ", "Hailstorm damage across the roof, bonnet, and left rear door. Multiple dents ranging from 1–3 cm in diameter.",           "approved");
  insert.run("BMW",       "3 Series",  "KL-789-MN", "Driver-side mirror assembly knocked off completely while parked in a narrow residential street. Housing cracked.",          "draft");
  insert.run("Renault",   "Clio",      "PQ-321-RS", "Rear-end collision at low speed. Boot lid no longer closes flush; rear bumper cracked on the right corner.",               "submitted");
  insert.run("Ford",      "Focus",     "TU-654-VW", "Keying damage running the full length of the passenger-side doors. Deep scratches through to the primer.",                 "approved");
  insert.run("Peugeot",   "208",       "XY-987-ZA", "Cracked windscreen from a stone chip that spread overnight. Crack originates at the lower-left corner and extends 30 cm.", "draft");
})();

console.log("Seeded 6 damage reports.");
