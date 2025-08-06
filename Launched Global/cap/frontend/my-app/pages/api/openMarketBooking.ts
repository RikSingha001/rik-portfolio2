import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  const db = await connectToDatabase();

  if (req.method === 'GET') {
    try {
      const [rows] = await db.execute(
        'SELECT * FROM open_market_requests WHERE status = "open" ORDER BY release_time DESC'
      );
      res.status(200).json({ requests: rows });
    } catch (error) {
      console.error('GET error:', error);
      res.status(500).json({ message: 'Failed to fetch requests' });
    }
  }

  else if (req.method === 'POST') {
    try {
      const { booking_id, vendor_id, region } = req.body;
      const releaseTime = new Date();

      await db.execute(
        `INSERT INTO open_market_requests (booking_id, released_by, region, release_time, status, whitelisted_only)
         VALUES (?, ?, ?, ?, 'open', 1)`,
        [booking_id, vendor_id, region, releaseTime]
      );

      res.status(201).json({ success: true, message: 'Request added to Open Market' });
    } catch (error) {
      console.error('POST error:', error);
      res.status(500).json({ message: 'Failed to add request' });
    }
  }

  else {
    res.setHeader('Allow', ['GET', 'POST']);
    res.status(405).end(`Method ${req.method} Not Allowed`);
  }
}

