// file: backend/vendor_registration.js
import express from 'express';
import mysql from 'mysql2';
import dotenv from 'dotenv';

dotenv.config();

const router = express.Router();

// MySQL connection
const db = mysql.createConnection({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'cab',
});

db.connect((err) => {
  if (err) {
    console.error('❌ Failed to connect to MySQL:', err);
    process.exit(1);
  }
  console.log('✅ Connected to MySQL');
});

// Register vendor route
router.post('/vendor-registration', (req, res) => {
  const {
    owner_name,
    company_email,
    company_licence,
    company_address,
    company_name,
  } = req.body;

  // ✅ Validate input
  if (!owner_name || !company_email || !company_licence || !company_address || !company_name) {
    return res.status(400).json({ error: 'All fields are required' });
  }

  // ✅ Check if vendor already exists
  const checkQuery = 'SELECT id FROM vendors_registration WHERE company_email = ? OR company_licence = ?';
  db.query(checkQuery, [company_email, company_licence], (checkErr, checkResults) => {
    if (checkErr) {
      console.error('Database error:', checkErr);
      return res.status(500).json({ error: 'Database error' });
    }

    if (checkResults.length > 0) {
      return res.status(409).json({ message: 'Vendor already exists' });
    }

    // ✅ Insert vendor
    const query = 'INSERT INTO vendors_registration (owner_name, company_email, company_licence, company_address, company_name) VALUES (?, ?, ?, ?, ?)';
    db.query(query, [owner_name, company_email, company_licence, company_address, company_name], (err, result) => {
      if (err) {
        console.error('Error inserting vendor:', err);
        return res.status(500).json({ error: 'Database error' });
      }

      console.log('Vendor registered successfully:', result);
      res.status(201).json({ message: 'Vendor registered successfully', vendor_id: result.insertId });
    });
  });
});

export default router;
