// File: backend/driver-login.js
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
  database: process.env.DB_NAME || 'cab_portal',
});

// POST /api/driver-login
router.post('/driver-login', (req, res) => {
  const { name, phone, password ,vehicle_number } = req.body;

  // Input validation
  if (!name || !phone || !password || !vehicle_number) {
    return res.status(400).json({ message: 'All fields are required.' });
  }

  const query = 'SELECT * FROM drivers WHERE name = ? AND phone = ? AND password = ? AND vehicle_number = ?';
  db.execute(query, [name, phone, password,vehicle_number], (err, results) => {
    if (err) {
      console.error('DB Error:', err);
      return res.status(500).json({ message: 'Database error.' });
    }

    if (results.length === 1) {
      return res.status(200).json({ message: 'Login successful', user: results[0] });
    } else {
      return res.status(401).json({ message: 'Invalid credentials.' });
    }
  });
});

export default router;
