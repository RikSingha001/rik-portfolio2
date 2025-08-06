// pages/api/book.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Method Not Allowed' });
  }

  const {
    pickup_location,
    drop_location,
    travel_date,
    pickup_time,
    drop_time,
    number_of_sit,
    role,
  } = req.body;

  if (
    !pickup_location || !drop_location || !travel_date ||
    !pickup_time || !drop_time || !number_of_sit || !role
  ) {
    return res.status(400).json({ message: 'Missing required fields' });
  }

  try {
    const db = await connectToDatabase();

    const [result]: any = await db.execute(
      `INSERT INTO bookings (
        pickup_location, drop_location, travel_date,
        pickup_time, drop_time, number_of_sit, role, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')`,
      [
        pickup_location,
        drop_location,
        travel_date,
        pickup_time,
        drop_time,
        parseInt(number_of_sit),
        role
      ]
    );

    const bookingId = result.insertId;

    await db.execute(
      `INSERT INTO open_market_requests (
        booking_id, release_time, status, whitelisted_only
      ) VALUES (?, NOW(), 'open', 1)`,
      [bookingId]
    );

    await db.end();

    return res.status(201).json({ message: 'Booking successful and open market request created' });
  } catch (error) {
    console.error('Booking Error:', error);
    return res.status(500).json({ message: 'Internal Server Error' });
  }
}
