// // file: app/api/trip/status/route.ts
// import { NextResponse } from 'next/server';

// let tripStatus: Record<number, 'upcoming' | 'ongoing' | 'completed'> = {};
 
// export async function POST(req: Request) {
//   const body = await req.json();
//   const { bookingId, action } = body;

//   if (!bookingId || !['start', 'end'].includes(action)) {
//     return NextResponse.json({ error: 'Invalid request' }, { status: 400 });
//   }

//   if (action === 'start') {
//     tripStatus[bookingId] = 'ongoing';
//   } else if (action === 'end') {
//     tripStatus[bookingId] = 'completed';
//   }

//   return NextResponse.json({ bookingId, status: tripStatus[bookingId] });
// }

// file: pages/api/trip/status.ts
import type { NextApiRequest, NextApiResponse } from 'next';
import { connectToDatabase } from '@/lib/db';

export default async function handler(req: NextApiRequest, res: NextApiResponse) {
  if (req.method !== 'POST') return res.status(405).json({ message: 'Method not allowed' });

  const { bookingId, action } = req.body;
  if (!bookingId || !['start', 'end'].includes(action)) {
    return res.status(400).json({ message: 'Invalid request' });
  }

  const newStatus = action === 'start' ? 'ongoing' : 'completed';

  try {
    const db = await connectToDatabase();
    await db.execute(
      `UPDATE bookings SET status = ? WHERE id = ?`,
      [newStatus, bookingId]
    );

    res.status(200).json({ status: newStatus });
  } catch (err) {
    console.error('Status update error:', err);
    res.status(500).json({ message: 'Server Error' });
  }
}
