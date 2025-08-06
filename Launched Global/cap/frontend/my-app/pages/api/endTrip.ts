import { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') return res.status(405).json({ message: 'Method Not Allowed' });

  const { booking_id, vendor_id } = req.body;

  try {
    const db = await connectToDatabase();
    await db.execute(
      `UPDATE bookings SET status = 'completed' WHERE id = ? AND assoc_vendor = ?`,
      [booking_id, vendor_id]
    );
    return res.status(200).json({ success: true, message: 'Trip ended successfully' });
  } catch (error) {
    return res.status(500).json({ success: false, message: 'Error ending trip', error });
  }
}
