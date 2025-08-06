import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  try {
    const db = await connectToDatabase();
    const [rows] = await db.execute('SELECT * FROM bookings WHERE status = ?');
    await db.end();
    res.status(200).json({ rides: rows });
  } catch (err) {
    res.status(500).json({ message: 'DB error', error: err });
  }
}
