import express from 'express';
import router from 'express';
import db from '../db';
const router = express.Router();
const db = require('../db');

router.post('/', (req, res) => {
  const {
    pickup_location,
    drop_location,
    travel_date,
    pickup_time,
    drop_time,
    number_of_sit,
    role,
    
  } = req.body;

  const query = `
    INSERT INTO bookings (
      pickup_location,
      drop_location,
      travel_date,
      pickup_time,
      drop_time,
      number_of_sit,
      role,      
    ) VALUES (?, ?, ?, ?, ?, ?, ?, )
  `;

  const values = [
    pickup_location,
    drop_location,
    travel_date,
    pickup_time,
    drop_time,
    number_of_sit,
    role|| null,
  ];

  db.query(query, values, (err, result) => {
    if (err) {
      console.error('🚫 Error inserting booking:', err.message);
      return res.status(500).json({ success: false, message: 'Failed to book ride' });
    }
    res.status(200).json({ success: true, bookingId: result.insertId });
  });
});

module.exports = router;
