import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') {
    return res.status(405).json({ message: 'Only POST allowed' });
  }

  const { requestId, vendorId } = req.body;

  if (!requestId || !vendorId) {
    return res.status(400).json({ success: false, message: 'Missing requestId or vendorId' });
  }

  try {
    const db = await connectToDatabase();

    // 1️⃣ Check if already accepted or closed
    const [checkRows]: any = await db.execute(
      `SELECT status FROM open_market_requests WHERE id = ?`,
      [requestId]
    );

    if (checkRows.length === 0 || checkRows[0].status !== 'open') {
      return res.status(400).json({ success: false, message: 'Request is no longer available' });
    }

    // 2️⃣ Mark the request as accepted
    await db.execute(
      `UPDATE open_market_requests SET status = 'accepted', accepted_by = ? WHERE id = ?`,
      [vendorId, requestId]
    );

    // (Optional) You may also update the original booking status if needed

    return res.status(200).json({ success: true, message: 'Booking accepted' });
  } catch (err) {
    console.error('Error accepting booking:', err);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
}
