// login.js
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

// POST /api/login
router.post('/login', (req, res) => {
  const { name, phone, password } = req.body;

  // Input validation
  if (!name || !phone || !password) {
    return res.status(400).json({ message: 'All fields are required.' });
  }

  const query = 'SELECT * FROM users WHERE name = ? AND phone = ? AND password = ?';
  db.execute(query, [name, phone, password], (err, results) => {
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
