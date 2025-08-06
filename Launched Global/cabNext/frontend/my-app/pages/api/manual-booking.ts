import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db'; 

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Only POST requests are allowed' });
  }

  const {
    guestName,
    pickup,
    drop,
    date,
    time,
    vehicleType
  } = req.body;

  // 🔎 Check for required fields
  if (!guestName || !pickup || !drop || !date || !time || !vehicleType) {
    return res.status(400).json({ message: 'Missing required fields' });
  }

  try {
    const db = await connectToDatabase();

    // 📝 Insert booking
    const [result]: any = await db.execute(
      `INSERT INTO bookings (guest_name, pickup, drop_location, date, time, vehicle_type, status)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [guestName, pickup, drop, date, time, vehicleType, 'upcoming']
    );

    const newBooking = {
      id: result.insertId,
      guestName,
      pickup,
      drop,
      date,
      time,
      vehicleType,
      status: 'upcoming'
    };

    return res.status(201).json(newBooking);
  } catch (err) {
    console.error('Database insert error:', err);
    return res.status(500).json({ message: 'Internal Server Error' });
  }
}
