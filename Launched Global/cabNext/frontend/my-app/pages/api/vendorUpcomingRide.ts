// ✅ file: /pages/api/vendorUpcomingRide.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  const { vendorId } = req.query;

  if (!vendorId) {
    return res.status(400).json({ message: 'Vendor ID missing' });
  }

  try {
    const db = await connectToDatabase();
    const [rows]: any = await db.execute(
      `SELECT * FROM bookings WHERE status = 'pending' AND assoc_vendor = ? LIMIT 1`,
      [vendorId]
    );
    await db.end();

    const ride = rows.length > 0 ? rows[0] : null;
    res.status(200).json({ ride });
  } catch (err) {
    res.status(500).json({ message: 'DB error', error: err });
  }
}
