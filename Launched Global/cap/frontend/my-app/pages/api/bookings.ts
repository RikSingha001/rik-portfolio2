// ✅ file: pages/api/bookings.ts

import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  try {
    const connection = await connectToDatabase();
    const [rows] = await connection.execute('SELECT * FROM bookings ORDER BY id DESC');
    await connection.end();

    res.status(200).json(rows);
  } catch (err) {
    res.status(500).json({ message: 'DB error', error: err });
  }
}
