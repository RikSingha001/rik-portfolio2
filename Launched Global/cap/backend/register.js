import express from 'express';
const router = express.Router();
const mysql = require('mysql2');
require('dotenv').config();

// MySQL connection
const db = mysql.createConnection({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'cab',
});

db.connect((err) => {
  if (err) throw err;
  console.log('Connected to MySQL');
});

// Register route
router.post('/register', (req, res) => {
  const {
    name,
    email,
    password,
    phone,
    aadhar_number,
    role,
    pan_number,
    license_number,
    vehicle_number,
    vehicle_model,
    vehicle_usage,
    vehicle_mileage,
    company_name,
    company_email,
    company_licence,
    company_address,
  } = req.body;

  if (!name || !email || !password || !phone || !aadhar_number || !role) {
    return res.status(400).json({ message: 'Required fields are missing' });
  }

  const userSql = `
    INSERT INTO users (name, email, password, phone, aadhar_number, role)
    VALUES (?, ?, ?, ?, ?, ?)
  `;

  const userValues = [name, email, password, phone, aadhar_number, role];

  db.query(userSql, userValues, (err, userResult) => {
    if (err) {
      console.error('User Insert Error:', err);
      return res.status(500).json({ message: 'Error inserting user' });
    }

    const userId = userResult.insertId;

    // Driver
    if (role === 'driver') {
      const driverSql = `
        INSERT INTO drivers (
          user_id, name, email, password, phone, aadhar_number,pan_number, license_number, vehicle_number,
          vehicle_model, vehicle_usage, vehicle_mileage
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
      `;

      const driverValues = [
        userId,
        name, email, password, phone, aadhar_number,
        pan_number,
        license_number,
        vehicle_number,
        vehicle_model,
        vehicle_usage,
        vehicle_mileage,
      ];

      db.query(driverSql, driverValues, (err) => {
        if (err) {
          console.error('Driver Insert Error:', err);
          return res.status(500).json({ message: 'Error inserting driver details' });
        }
        return res.status(201).json({ message: 'Driver registered successfully' });
      });

    // Vendor
    } else if (role === 'vendor') {
      const vendorSql = `
        INSERT INTO vendors (
          user_id, name, email, password, phone, aadhar_number, pan_number, license_number, vehicle_number,
          vehicle_model, vehicle_usage, vehicle_mileage, company_name,company_email,
    company_licence,
    company_address,
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
      `;

      const vendorValues = [
        userId,
        name, email, password, phone, aadhar_number,
        pan_number,
        license_number,
        vehicle_number,
        vehicle_model,
        vehicle_usage,
        vehicle_mileage,
        company_name,
        company_email,
    company_licence,
    company_address,
      ];

      db.query(vendorSql, vendorValues, (err) => {
        if (err) {
          console.error('Vendor Insert Error:', err);
          return res.status(500).json({ message: 'Error inserting vendor details' });
        }
        return res.status(201).json({ message: 'Vendor registered successfully' });
      });

    // Normal user
    } else {
      return res.status(201).json({ message: 'User registered successfully' });
    }
  });
});

module.exports = router;
